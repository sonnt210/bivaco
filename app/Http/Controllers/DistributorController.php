<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;

class DistributorController extends Controller
{
    /**
     * Trang chủ quản lý distributor
     */
    public function index(): View
    {
        $totalDistributors = User::whereNotNull('distributor_code')->count();
        $activeDistributors = User::whereNotNull('distributor_code')->where('status', 'active')->count();

        // Lấy thống kê theo cấp độ
        $levelStats = User::getLevelStatistics();

        // Lấy tất cả cấp độ có trong hệ thống
        $allLevels = User::getAllLevels();

        $recentDistributors = User::whereNotNull('distributor_code')->latest()->take(10)->get();

        return view('distributors.index', compact(
            'totalDistributors',
            'activeDistributors',
            'levelStats',
            'allLevels',
            'recentDistributors'
        ));
    }

    /**
     * Hiển thị danh sách theo cấp độ
     */
    public function showByLevel($level): View
    {
        $distributors = User::whereNotNull('distributor_code')
            ->byLevel($level)
            ->where('status', 'active')
            ->with('parent')
            ->paginate(20);

        $levelName = 'F' . $level;
        $levelDescription = User::getLevelDescription($level);

        // Lấy thống kê cho cấp độ này
        $levelStats = User::getLevelStatistics();
        $currentLevelStats = $levelStats["F{$level}"] ?? null;

        return view('distributors.by-level', compact(
            'distributors',
            'level',
            'levelName',
            'levelDescription',
            'currentLevelStats'
        ));
    }

    /**
     * Hiển thị thống kê tổng quan
     */
    public function showStatistics(): View
    {
        $statistics = User::getLevelStatistics();
        $allLevels = User::getAllLevels();

        // Thống kê tổng quan
        $totalDistributors = User::whereNotNull('distributor_code')->count();
        $totalSales = Order::sum('amount');
        $totalOrders = Order::count();

        return view('distributors.statistics', compact(
            'statistics',
            'allLevels',
            'totalDistributors',
            'totalSales',
            'totalOrders'
        ));
    }

    /**
     * Hiển thị chi tiết distributor
     */
    public function show($id): View
    {
        $distributor = User::whereNotNull('distributor_code')
            ->with(['parent', 'children', 'orders'])
            ->findOrFail($id);

        $levelName = $distributor->getLevelName();
        $totalSales = $distributor->orders->sum('amount');
        $recentOrders = $distributor->orders()->latest()->take(10)->get();

        // Lấy thông tin network
        $networkOverview = $distributor->getDistributorNetworkOverview();

        // Lấy tổ tiên
        $ancestors = $distributor->getAllAncestors();

        return view('distributors.show', compact(
            'distributor',
            'levelName',
            'totalSales',
            'recentOrders',
            'networkOverview',
            'ancestors'
        ));
    }

    /**
     * Hiển thị cây phân cấp
     */
    public function showTree($id): View
    {
        $distributor = User::whereNotNull('distributor_code')
            ->with(['children.children', 'parent'])
            ->findOrFail($id);

        $tree = $this->buildTree($distributor);
        $maxDepth = $this->getMaxDepth($distributor);

        return view('distributors.tree', compact('distributor', 'tree', 'maxDepth'));
    }

    /**
     * Form tạo mới distributor
     */
    public function create(): View
    {
        $parents = User::whereNotNull('distributor_code')->where('status', 'active')->get();
        $allLevels = User::getAllLevels();
        $maxLevel = config('distributor.max_level', 10);

        return view('distributors.create', compact('parents', 'allLevels', 'maxLevel'));
    }

    /**
     * Lưu distributor mới
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'distributor_code' => 'required|unique:users',
            'distributor_name' => 'required|string|max:255',
            'distributor_email' => 'required|email|unique:users',
            'distributor_phone' => 'required|string|max:20',
            'distributor_address' => 'required|string',
            'parent_id' => 'nullable|exists:users,id',
            'join_date' => 'required|date'
        ]);

        $level = 1;
        if ($request->parent_id) {
            $parent = User::find($request->parent_id);
            $level = $parent->level + 1;

            // Kiểm tra giới hạn cấp độ
            $maxLevel = config('distributor.max_level', 10);
            if ($level > $maxLevel) {
                return back()->withErrors(['parent_id' => "Không thể tạo distributor vượt quá cấp độ {$maxLevel}"]);
            }
        }

        User::create([
            'name' => $request->distributor_name,
            'email' => $request->distributor_email,
            'distributor_code' => $request->distributor_code,
            'distributor_name' => $request->distributor_name,
            'distributor_email' => $request->distributor_email,
            'distributor_phone' => $request->distributor_phone,
            'distributor_address' => $request->distributor_address,
            'parent_id' => $request->parent_id,
            'level' => $level,
            'join_date' => $request->join_date,
            'status' => 'active'
        ]);

        return redirect()->route('distributors.index')
            ->with('success', 'Distributor đã được tạo thành công!');
    }

    /**
     * Form chỉnh sửa distributor
     */
    public function edit($id): View
    {
        $distributor = User::whereNotNull('distributor_code')->findOrFail($id);
        $parents = User::whereNotNull('distributor_code')
            ->where('id', '!=', $id)
            ->where('status', 'active')
            ->get();
        $allLevels = User::getAllLevels();

        return view('distributors.edit', compact('distributor', 'parents', 'allLevels'));
    }

    /**
     * Cập nhật distributor
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $distributor = User::whereNotNull('distributor_code')->findOrFail($id);

        $request->validate([
            'distributor_code' => 'required|unique:users,distributor_code,' . $id,
            'distributor_name' => 'required|string|max:255',
            'distributor_email' => 'required|email|unique:users,distributor_email,' . $id,
            'distributor_phone' => 'required|string|max:20',
            'distributor_address' => 'required|string',
            'parent_id' => 'nullable|exists:users,id',
            'join_date' => 'required|date',
            'status' => 'required|in:active,inactive'
        ]);

        $level = 1;
        if ($request->parent_id) {
            $parent = User::find($request->parent_id);
            $level = $parent->level + 1;

            // Kiểm tra giới hạn cấp độ
            $maxLevel = config('distributor.max_level', 10);
            if ($level > $maxLevel) {
                return back()->withErrors(['parent_id' => "Không thể tạo distributor vượt quá cấp độ {$maxLevel}"]);
            }
        }

        $distributor->update([
            'name' => $request->distributor_name,
            'email' => $request->distributor_email,
            'distributor_code' => $request->distributor_code,
            'distributor_name' => $request->distributor_name,
            'distributor_email' => $request->distributor_email,
            'distributor_phone' => $request->distributor_phone,
            'distributor_address' => $request->distributor_address,
            'parent_id' => $request->parent_id,
            'level' => $level,
            'join_date' => $request->join_date,
            'status' => $request->status
        ]);

        return redirect()->route('distributors.index')
            ->with('success', 'Distributor đã được cập nhật thành công!');
    }

    /**
     * Xóa distributor
     */
    public function destroy($id): RedirectResponse
    {
        $distributor = User::whereNotNull('distributor_code')->findOrFail($id);

        // Kiểm tra xem có con không
        if ($distributor->children()->count() > 0) {
            return back()->withErrors(['error' => 'Không thể xóa distributor có con!']);
        }

        // Kiểm tra xem có đơn hàng không
        if ($distributor->orders()->count() > 0) {
            return back()->withErrors(['error' => 'Không thể xóa distributor có đơn hàng!']);
        }

        $distributor->delete();

        return redirect()->route('distributors.index')
            ->with('success', 'Distributor đã được xóa thành công!');
    }

    /**
     * Tìm kiếm distributor
     */
    public function search(Request $request): View
    {
        $query = $request->get('q');
        
        $distributors = User::whereNotNull('distributor_code')
            ->where(function($q) use ($query) {
                $q->where('distributor_name', 'like', "%{$query}%")
                  ->orWhere('distributor_code', 'like', "%{$query}%")
                  ->orWhere('distributor_email', 'like', "%{$query}%");
            })
            ->with('parent')
            ->paginate(20);

        return view('distributors.search', compact('distributors', 'query'));
    }

    /**
     * Xây dựng cây phân cấp
     */
    private function buildTree($distributor): array
    {
        $tree = [
            'id' => $distributor->id,
            'name' => $distributor->distributor_name,
            'code' => $distributor->distributor_code,
            'level' => $distributor->level,
            'children' => []
        ];

        foreach ($distributor->children as $child) {
            $tree['children'][] = $this->buildTree($child);
        }

        return $tree;
    }

    /**
     * Lấy độ sâu tối đa của cây
     */
    private function getMaxDepth($distributor, $currentDepth = 0): int
    {
        if ($distributor->children->isEmpty()) {
            return $currentDepth;
        }

        $maxDepth = $currentDepth;
        foreach ($distributor->children as $child) {
            $depth = $this->getMaxDepth($child, $currentDepth + 1);
            $maxDepth = max($maxDepth, $depth);
        }

        return $maxDepth;
    }

    /**
     * Hiển thị chi tiết thu nhập
     */
    public function showIncomeDetails($id): View
    {
        $distributor = User::whereNotNull('distributor_code')
            ->with(['orders', 'bonusRecords'])
            ->findOrFail($id);

        $levelName = $distributor->getLevelName();
        $currentMonth = \App\Models\BonusRecord::getCurrentMonth();
        $bonusInfo = app(\App\Services\BonusCalculationService::class)
            ->calculateDistributorBonus($distributor, $currentMonth);
        $bonusHistory = \App\Models\BonusRecord::where('user_id', $distributor->id)
            ->orderBy('month_year', 'desc')
            ->limit(12)
            ->get();

        // Thống kê theo tháng
        $monthlyStats = $this->getMonthlyStats($distributor->id) ?? [];
        // Thống kê theo năm
        $yearlyStats = $this->getYearlyStats($distributor->id) ?? [];
        // Top performers trong network
        $topPerformers = $this->getTopPerformers($distributor->id) ?? [];
        // Tổng thu nhập
        $totalIncome = $distributor->bonusRecords->sum('bonus_amount');
        $totalSales = $distributor->orders->sum('amount');

        return view('distributors.income-details', compact(
            'distributor',
            'monthlyStats',
            'yearlyStats',
            'topPerformers',
            'totalIncome',
            'totalSales',
            'levelName',
            'currentMonth',
            'bonusInfo',
            'bonusHistory'
        ));
    }

    /**
     * Lấy thống kê theo tháng
     */
    private function getMonthlyStats($distributorId): array
    {
        $currentYear = date('Y');
        
        return DB::table('bonus_records')
            ->where('user_id', $distributorId)
            ->whereYear('created_at', $currentYear)
            ->selectRaw('MONTH(created_at) as month, SUM(bonus_amount) as total_bonus, COUNT(*) as bonus_count')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month')
            ->toArray();
    }

    /**
     * Lấy thống kê theo năm
     */
    private function getYearlyStats($distributorId): array
    {
        return DB::table('bonus_records')
            ->where('user_id', $distributorId)
            ->selectRaw('YEAR(created_at) as year, SUM(bonus_amount) as total_bonus, COUNT(*) as bonus_count')
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Lấy top performers trong network
     */
    private function getTopPerformers($distributorId): array
    {
        return User::whereNotNull('distributor_code')
            ->where('parent_id', $distributorId)
            ->withSum('orders', 'amount')
            ->orderByDesc('orders_sum_amount')
            ->take(5)
            ->get()
            ->toArray();
    }
}
