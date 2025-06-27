<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BonusRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'month_year',
        'personal_sales',
        'personal_sales_t1',
        'personal_sales_t2',
        'branch_sales',
        'branch_sales_t1',
        'branch_sales_t2',
        'qualified_branches',
        'qualified_branches_t1',
        'qualified_branches_t2',
        'personal_condition_met',
        'branch_condition_met',
        'is_qualified',
        'bonus_amount',
        'consecutive_failures',
        'title_lost',
        'notes'
    ];

    protected $casts = [
        'personal_sales' => 'decimal:2',
        'personal_sales_t1' => 'decimal:2',
        'personal_sales_t2' => 'decimal:2',
        'branch_sales' => 'decimal:2',
        'branch_sales_t1' => 'decimal:2',
        'branch_sales_t2' => 'decimal:2',
        'bonus_amount' => 'decimal:2',
        'personal_condition_met' => 'boolean',
        'branch_condition_met' => 'boolean',
        'is_qualified' => 'boolean',
        'title_lost' => 'boolean',
    ];

    // Constants
    const PERSONAL_SALES_THRESHOLD = 5000000;
    const BRANCH_SALES_THRESHOLD = 250000000;
    const MIN_QUALIFIED_BRANCHES = 2; // Tối thiểu 2 nhánh đạt chuẩn
    const MAX_CONSECUTIVE_FAILURES = 5; // Tối đa 5 tháng liên tiếp không đạt
    const BONUS_PERCENTAGE = 0.01; // 1% tổng doanh số hệ thống

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Kiểm tra điều kiện doanh số cá nhân (3 tháng liên tục >= 5 triệu)
     */
    public function checkPersonalCondition(): bool
    {
        return $this->personal_sales >= self::PERSONAL_SALES_THRESHOLD &&
               $this->personal_sales_t1 >= self::PERSONAL_SALES_THRESHOLD &&
               $this->personal_sales_t2 >= self::PERSONAL_SALES_THRESHOLD;
    }

    /**
     * Kiểm tra điều kiện doanh số nhánh (3 tháng liên tục có ít nhất 2 nhánh đạt chuẩn)
     */
    public function checkBranchCondition(): bool
    {
        return $this->qualified_branches >= self::MIN_QUALIFIED_BRANCHES &&
               $this->qualified_branches_t1 >= self::MIN_QUALIFIED_BRANCHES &&
               $this->qualified_branches_t2 >= self::MIN_QUALIFIED_BRANCHES;
    }

    /**
     * Kiểm tra xem có đủ điều kiện nhận thưởng không
     */
    public function checkQualification(): bool
    {
        if ($this->title_lost) {
            return false;
        }

        $personalCondition = $this->checkPersonalCondition();
        $branchCondition = $this->checkBranchCondition();

        return $personalCondition && $branchCondition;
    }

    /**
     * Cập nhật số tháng liên tiếp không đạt 5 triệu
     */
    public function updateConsecutiveFailures(): void
    {
        if ($this->personal_sales < self::PERSONAL_SALES_THRESHOLD) {
            $this->consecutive_failures++;
        } else {
            $this->consecutive_failures = 0;
        }

        // Kiểm tra mất danh hiệu
        if ($this->consecutive_failures >= self::MAX_CONSECUTIVE_FAILURES) {
            $this->title_lost = true;
        }
    }

    /**
     * Tính toán và cập nhật tất cả điều kiện
     */
    public function calculateConditions(): void
    {
        $this->personal_condition_met = $this->checkPersonalCondition();
        $this->branch_condition_met = $this->checkBranchCondition();
        $this->updateConsecutiveFailures();
        $this->is_qualified = $this->checkQualification();
    }

    /**
     * Lấy tháng trước đó
     */
    public static function getPreviousMonth(string $monthYear): string
    {
        $date = Carbon::createFromFormat('Y-m', $monthYear);
        return $date->subMonth()->format('Y-m');
    }

    /**
     * Lấy tháng hiện tại
     */
    public static function getCurrentMonth(): string
    {
        return Carbon::now()->format('Y-m');
    }

    /**
     * Lấy tháng trước đó 2 tháng
     */
    public static function getTwoMonthsAgo(string $monthYear): string
    {
        $date = Carbon::createFromFormat('Y-m', $monthYear);
        return $date->subMonths(2)->format('Y-m');
    }
}
