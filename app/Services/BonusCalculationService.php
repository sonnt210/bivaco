<?php

namespace App\Services;

use App\Models\BonusRecord;
use App\Models\User;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BonusCalculationService
{
    /**
     * Tính toán thưởng đồng chia cho tháng cụ thể
     */
    public function calculateBonusForMonth(string $monthYear = null): array
    {
        $monthYear = $monthYear ?? BonusRecord::getCurrentMonth();

        // Lấy tất cả nhà phân phối
        $distributors = User::whereNotNull('distributor_code')->where('status', 'active')->get();

        $results = [];
        $qualifiedCount = 0;
        $totalSystemSales = $this->getTotalSystemSales($monthYear);

        foreach ($distributors as $distributor) {
            $result = $this->calculateDistributorBonus($distributor, $monthYear);
            $results[] = $result;

            if ($result['is_qualified']) {
                $qualifiedCount++;
            }
        }

        // Tính quỹ thưởng
        $bonusFund = $totalSystemSales * BonusRecord::BONUS_PERCENTAGE;
        $bonusPerDistributor = $qualifiedCount > 0 ? $bonusFund / $qualifiedCount : 0;

        // Cập nhật số tiền thưởng cho từng nhà phân phối
        foreach ($results as &$result) {
            if ($result['is_qualified']) {
                $result['bonus_amount'] = $bonusPerDistributor;
                $this->updateBonusRecord($result);
            }
        }

        return [
            'month_year' => $monthYear,
            'total_system_sales' => $totalSystemSales,
            'bonus_fund' => $bonusFund,
            'qualified_count' => $qualifiedCount,
            'bonus_per_distributor' => $bonusPerDistributor,
            'results' => $results
        ];
    }

    /**
     * Tính toán thưởng cho một nhà phân phối cụ thể
     */
    public function calculateDistributorBonus(User $distributor, string $monthYear): array
    {
        // Lấy doanh số cá nhân 3 tháng liên tiếp
        $personalSales = $this->getPersonalSales($distributor->id, $monthYear);
        $personalSalesT1 = $this->getPersonalSales($distributor->id, BonusRecord::getPreviousMonth($monthYear));
        $personalSalesT2 = $this->getPersonalSales($distributor->id, BonusRecord::getTwoMonthsAgo($monthYear));

        // Lấy doanh số nhánh và số nhánh đạt chuẩn 3 tháng liên tiếp
        $branchData = $this->getBranchSales($distributor->id, $monthYear);
        $branchDataT1 = $this->getBranchSales($distributor->id, BonusRecord::getPreviousMonth($monthYear));
        $branchDataT2 = $this->getBranchSales($distributor->id, BonusRecord::getTwoMonthsAgo($monthYear));

        // Lấy record cũ hoặc tạo mới
        $bonusRecord = BonusRecord::firstOrNew([
            'user_id' => $distributor->id,
            'month_year' => $monthYear
        ]);

        // Cập nhật dữ liệu
        $bonusRecord->fill([
            'personal_sales' => $personalSales,
            'personal_sales_t1' => $personalSalesT1,
            'personal_sales_t2' => $personalSalesT2,
            'branch_sales' => $branchData['total_sales'],
            'branch_sales_t1' => $branchDataT1['total_sales'],
            'branch_sales_t2' => $branchDataT2['total_sales'],
            'qualified_branches' => $branchData['qualified_count'],
            'qualified_branches_t1' => $branchDataT1['qualified_count'],
            'qualified_branches_t2' => $branchDataT2['qualified_count'],
        ]);

        // Tính toán điều kiện
        $bonusRecord->calculateConditions();

        // Lưu record
        $bonusRecord->save();

        return [
            'distributor_id' => $distributor->id,
            'distributor_name' => $distributor->distributor_name,
            'distributor_code' => $distributor->distributor_code,
            'personal_sales' => $personalSales,
            'personal_sales_t1' => $personalSalesT1,
            'personal_sales_t2' => $personalSalesT2,
            'branch_sales' => $branchData['total_sales'],
            'branch_sales_t1' => $branchDataT1['total_sales'],
            'branch_sales_t2' => $branchDataT2['total_sales'],
            'qualified_branches' => $branchData['qualified_count'],
            'qualified_branches_t1' => $branchDataT1['qualified_count'],
            'qualified_branches_t2' => $branchDataT2['qualified_count'],
            'personal_condition_met' => $bonusRecord->personal_condition_met,
            'branch_condition_met' => $bonusRecord->branch_condition_met,
            'is_qualified' => $bonusRecord->is_qualified,
            'consecutive_failures' => $bonusRecord->consecutive_failures,
            'title_lost' => $bonusRecord->title_lost,
            'bonus_amount' => $bonusRecord->bonus_amount,
            'notes' => $this->generateNotes($bonusRecord)
        ];
    }

    /**
     * Lấy doanh số cá nhân của nhà phân phối trong tháng
     */
    private function getPersonalSales(int $distributorId, string $monthYear): float
    {
        $startDate = Carbon::createFromFormat('Y-m', $monthYear)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $monthYear)->endOfMonth();

        return Order::where('user_id', $distributorId)
            ->whereBetween('sale_time', [$startDate, $endDate])
            ->sum('amount');
    }

    /**
     * Lấy doanh số nhánh và số nhánh đạt chuẩn
     */
    private function getBranchSales(int $distributorId, string $monthYear): array
    {
        $startDate = Carbon::createFromFormat('Y-m', $monthYear)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $monthYear)->endOfMonth();

        // Lấy tất cả con trực tiếp
        $children = User::whereNotNull('distributor_code')->where('parent_id', $distributorId)->get();

        $totalSales = 0;
        $qualifiedCount = 0;

        foreach ($children as $child) {
            $childSales = Order::where('user_id', $child->id)
                ->whereBetween('sale_time', [$startDate, $endDate])
                ->sum('amount');

            $totalSales += $childSales;

            if ($childSales >= BonusRecord::BRANCH_SALES_THRESHOLD) {
                $qualifiedCount++;
            }
        }

        return [
            'total_sales' => $totalSales,
            'qualified_count' => $qualifiedCount
        ];
    }

    /**
     * Lấy tổng doanh số toàn hệ thống trong tháng
     */
    private function getTotalSystemSales(string $monthYear): float
    {
        $startDate = Carbon::createFromFormat('Y-m', $monthYear)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $monthYear)->endOfMonth();

        return Order::whereBetween('sale_time', [$startDate, $endDate])
            ->sum('amount');
    }

    /**
     * Cập nhật bonus record
     */
    private function updateBonusRecord(array $result): void
    {
        BonusRecord::where('user_id', $result['distributor_id'])
            ->where('month_year', $result['month_year'] ?? BonusRecord::getCurrentMonth())
            ->update([
                'bonus_amount' => $result['bonus_amount']
            ]);
    }

    /**
     * Tạo ghi chú cho record
     */
    private function generateNotes(BonusRecord $record): string
    {
        $notes = [];

        if ($record->title_lost) {
            $notes[] = "Mất danh hiệu do không đạt 5 triệu liên tục {$record->consecutive_failures} tháng";
        }

        if (!$record->personal_condition_met) {
            $notes[] = "Không đạt điều kiện doanh số cá nhân 5 triệu";
        }

        if (!$record->branch_condition_met) {
            $notes[] = "Không đạt điều kiện nhánh (ít nhất 1 nhánh đạt 3 triệu)";
        }

        return implode('; ', $notes);
    }

    /**
     * Lấy báo cáo thưởng cho tháng
     */
    public function getBonusReport(string $monthYear = null): array
    {
        $monthYear = $monthYear ?? BonusRecord::getCurrentMonth();

        $records = BonusRecord::with('user')
            ->where('month_year', $monthYear)
            ->orderBy('bonus_amount', 'desc')
            ->get();

        $totalBonus = $records->sum('bonus_amount');
        $qualifiedCount = $records->where('is_qualified', true)->count();
        $totalDistributors = $records->count();

        return [
            'month_year' => $monthYear,
            'records' => $records,
            'total_bonus' => $totalBonus,
            'qualified_count' => $qualifiedCount,
            'total_distributors' => $totalDistributors
        ];
    }
}
