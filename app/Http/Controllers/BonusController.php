<?php

namespace App\Http\Controllers;

use App\Models\BonusRecord;
use App\Models\User;
use App\Services\BonusCalculationService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BonusController extends Controller
{
    protected $bonusService;

    public function __construct(BonusCalculationService $bonusService)
    {
        $this->bonusService = $bonusService;
    }

    /**
     * Hiển thị trang quản lý thưởng
     */
    public function index(Request $request)
    {
        $monthYear = $request->get('month', BonusRecord::getCurrentMonth());

        $report = $this->bonusService->getBonusReport($monthYear);

        // Lấy danh sách tháng có dữ liệu
        $availableMonths = BonusRecord::select('month_year')
            ->distinct()
            ->orderBy('month_year', 'desc')
            ->pluck('month_year');

        return view('bonus.index', compact('report', 'availableMonths', 'monthYear'));
    }

    /**
     * Tính toán thưởng cho tháng hiện tại
     */
    public function calculate(Request $request)
    {
        $monthYear = $request->get('month', BonusRecord::getCurrentMonth());

        try {
            $result = $this->bonusService->calculateBonusForMonth($monthYear);

            return redirect()->route('bonus.index', ['month' => $monthYear])
                ->with('success', "Đã tính toán thưởng thành công cho tháng {$monthYear}.
                    Quỹ thưởng: " . number_format($result['bonus_fund']) . " VNĐ,
                    Số người đủ điều kiện: {$result['qualified_count']}");
        } catch (\Exception $e) {
            return redirect()->route('bonus.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Xem chi tiết thưởng của một nhà phân phối
     */
    public function show($distributorId, Request $request)
    {
        $distributor = User::whereNotNull('distributor_code')->findOrFail($distributorId);
        $monthYear = $request->get('month', BonusRecord::getCurrentMonth());

        // Lấy record thưởng
        $bonusRecord = BonusRecord::where('user_id', $distributorId)
            ->where('month_year', $monthYear)
            ->first();

        if (!$bonusRecord) {
            // Tính toán nếu chưa có
            $result = $this->bonusService->calculateDistributorBonus($distributor, $monthYear);
            $bonusRecord = BonusRecord::where('user_id', $distributorId)
                ->where('month_year', $monthYear)
                ->first();
        }

        // Lấy lịch sử thưởng
        $bonusHistory = BonusRecord::where('user_id', $distributorId)
            ->orderBy('month_year', 'desc')
            ->limit(12)
            ->get();

        return view('bonus.show', compact('distributor', 'bonusRecord', 'bonusHistory', 'monthYear'));
    }
}
