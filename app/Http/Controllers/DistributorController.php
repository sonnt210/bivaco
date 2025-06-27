<?php

namespace App\Http\Controllers;

use App\Models\Distributor;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DistributorController extends Controller
{
    /**
     * Trang chủ quản lý distributor
     */
    public function index(): View
    {
        $totalDistributors = Distributor::count();
        $activeDistributors = Distributor::active()->count();

        // Lấy thống kê theo cấp độ
        $levelStats = Distributor::getLevelStatistics();

        // Lấy tất cả cấp độ có trong hệ thống
        $allLevels = Distributor::getAllLevels();

        $recentDistributors = Distributor::latest()->take(10)->get();

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
        $distributors = Distributor::byLevel($level)
            ->active()
            ->with('parent')
            ->paginate(20);

        $levelName = 'F' . $level;
        $levelDescription = Distributor::getLevelDescription($level);

        // Lấy thống kê cho cấp độ này
        $levelStats = Distributor::getLevelStatistics();
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
        $statistics = Distributor::getLevelStatistics();
        $allLevels = Distributor::getAllLevels();

        // Thống kê tổng quan
        $totalDistributors = Distributor::count();
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
        $distributor = Distributor::with(['parent', 'children', 'orders'])
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
        $distributor = Distributor::with(['children.children', 'parent'])
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
        $parents = Distributor::active()->get();
        $allLevels = Distributor::getAllLevels();
        $maxLevel = config('distributor.max_level', 10);

        return view('distributors.create', compact('parents', 'allLevels', 'maxLevel'));
    }

    /**
     * Lưu distributor mới
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'distributor_code' => 'required|unique:distributors',
            'distributor_name' => 'required|string|max:255',
            'distributor_email' => 'required|email|unique:distributors',
            'distributor_phone' => 'required|string|max:20',
            'distributor_address' => 'required|string',
            'parent_id' => 'nullable|exists:distributors,id',
            'join_date' => 'required|date'
        ]);

        $level = 1;
        if ($request->parent_id) {
            $parent = Distributor::find($request->parent_id);
            $level = $parent->level + 1;

            // Kiểm tra giới hạn cấp độ
            $maxLevel = config('distributor.max_level', 10);
            if ($level > $maxLevel) {
                return back()->withErrors(['parent_id' => "Không thể tạo distributor vượt quá cấp độ {$maxLevel}"]);
            }
        }

        Distributor::create([
            'distributor_code' => $request->distributor_code,
            'distributor_name' => $request->distributor_name,
            'distributor_email' => $request->distributor_email,
            'distributor_phone' => $request->distributor_phone,
            'distributor_address' => $request->distributor_address,
            'parent_id' => $request->parent_id,
            'level' => $level,
            'join_date' => $request->join_date
        ]);

        return redirect()->route('distributors.index')
            ->with('success', 'Distributor đã được tạo thành công!');
    }

    /**
     * Form chỉnh sửa distributor
     */
    public function edit($id): View
    {
        $distributor = Distributor::findOrFail($id);
        $parents = Distributor::where('id', '!=', $id)->active()->get();
        $allLevels = Distributor::getAllLevels();

        return view('distributors.edit', compact('distributor', 'parents', 'allLevels'));
    }

    /**
     * Cập nhật distributor
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $distributor = Distributor::findOrFail($id);

        $request->validate([
            'distributor_code' => 'required|unique:distributors,distributor_code,' . $id,
            'distributor_name' => 'required|string|max:255',
            'distributor_email' => 'required|email|unique:distributors,distributor_email,' . $id,
            'distributor_phone' => 'required|string|max:20',
            'distributor_address' => 'required|string',
            'parent_id' => 'nullable|exists:distributors,id',
            'join_date' => 'required|date',
            'status' => 'required|in:active,inactive,suspended'
        ]);

        $level = 1;
        if ($request->parent_id) {
            $parent = Distributor::find($request->parent_id);
            $level = $parent->level + 1;

            // Kiểm tra giới hạn cấp độ
            $maxLevel = config('distributor.max_level', 10);
            if ($level > $maxLevel) {
                return back()->withErrors(['parent_id' => "Không thể chuyển distributor vượt quá cấp độ {$maxLevel}"]);
            }
        }

        $distributor->update([
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

        return redirect()->route('distributors.index', $id)
            ->with('success', 'Distributor đã được cập nhật thành công!');
    }

    /**
     * Xóa distributor
     */
    public function destroy($id): RedirectResponse
    {
        $distributor = Distributor::findOrFail($id);

        // Kiểm tra xem có con không
        if ($distributor->children()->count() > 0) {
            return back()->withErrors(['delete' => 'Không thể xóa distributor có con. Vui lòng xóa các distributor con trước.']);
        }

        $distributor->delete();

        return redirect()->route('distributors.index')
            ->with('success', 'Distributor đã được xóa thành công!');
    }

    /**
     * Xây dựng cây phân cấp
     */
    private function buildTree($distributor): array
    {
        $node = [
            'id' => $distributor->id,
            'name' => $distributor->distributor_name,
            'code' => $distributor->distributor_code,
            'level' => $distributor->getLevelName(),
            'level_number' => $distributor->level,
            'status' => $distributor->status,
            'children' => []
        ];

        foreach ($distributor->children as $child) {
            $node['children'][] = $this->buildTree($child);
        }

        return $node;
    }

    /**
     * Lấy độ sâu tối đa của cây
     */
    private function getMaxDepth($distributor, $currentDepth = 0): int
    {
        $maxDepth = $currentDepth;

        foreach ($distributor->children as $child) {
            $childDepth = $this->getMaxDepth($child, $currentDepth + 1);
            $maxDepth = max($maxDepth, $childDepth);
        }

        return $maxDepth;
    }

    /**
     * Hiển thị thông tin chi tiết về doanh số và thu nhập của distributor
     */
    public function showIncomeDetails($id): View
    {
        $distributor = Distributor::with(['parent', 'children', 'orders'])
            ->findOrFail($id);

        $levelName = $distributor->getLevelName();

        // Thống kê doanh số của distributor này
        $ownSales = $distributor->orders->sum('amount');
        $ownOrders = $distributor->orders->count();
        $averageOrderValue = $ownOrders > 0 ? $ownSales / $ownOrders : 0;

        // Thống kê doanh số của toàn bộ network (bao gồm con cháu)
        $networkOverview = $distributor->getDistributorNetworkOverview();
        $totalNetworkSales = $networkOverview['total_sales'];

        // Lấy danh sách con trực tiếp với thống kê
        $directChildren = $distributor->children()->with(['orders'])->get();
        $childrenStats = [];

        foreach ($directChildren as $child) {
            $childSales = $child->orders->sum('amount');
            $childOrders = $child->orders->count();
            $childNetworkSales = $child->getTotalSubNetworkSales();

            $childrenStats[] = [
                'distributor' => $child,
                'own_sales' => $childSales,
                'own_orders' => $childOrders,
                'network_sales' => $childNetworkSales,
                'average_order' => $childOrders > 0 ? $childSales / $childOrders : 0
            ];
        }

        // Thống kê theo thời gian
        $monthlyStats = $this->getMonthlyStats($distributor->id);
        $yearlyStats = $this->getYearlyStats($distributor->id);

        // Top performers trong network
        $topPerformers = $this->getTopPerformers($distributor->id);

        // Thông tin thưởng đồng chia
        $bonusService = app(\App\Services\BonusCalculationService::class);
        $currentMonth = \App\Models\BonusRecord::getCurrentMonth();
        $bonusInfo = $bonusService->calculateDistributorBonus($distributor, $currentMonth);
        
        // Lấy lịch sử thưởng
        $bonusHistory = \App\Models\BonusRecord::where('distributor_id', $distributor->id)
            ->orderBy('month_year', 'desc')
            ->limit(12)
            ->get();

        return view('distributors.income-details', compact(
            'distributor',
            'levelName',
            'ownSales',
            'ownOrders',
            'averageOrderValue',
            'totalNetworkSales',
            'childrenStats',
            'monthlyStats',
            'yearlyStats',
            'topPerformers',
            'bonusInfo',
            'bonusHistory',
            'currentMonth'
        ));
    }

    /**
     * Lấy thống kê theo tháng
     */
    private function getMonthlyStats($distributorId): array
    {
        $stats = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('Y-m');

            $sales = Order::where('distributor_id', $distributorId)
                ->whereYear('sale_time', $date->year)
                ->whereMonth('sale_time', $date->month)
                ->sum('amount');

            $orders = Order::where('distributor_id', $distributorId)
                ->whereYear('sale_time', $date->year)
                ->whereMonth('sale_time', $date->month)
                ->count();

            $stats[$month] = [
                'sales' => $sales,
                'orders' => $orders,
                'average' => $orders > 0 ? $sales / $orders : 0
            ];
        }

        return $stats;
    }

    /**
     * Lấy thống kê theo năm
     */
    private function getYearlyStats($distributorId): array
    {
        $stats = [];

        for ($i = 4; $i >= 0; $i--) {
            $year = now()->subYears($i)->year;

            $sales = Order::where('distributor_id', $distributorId)
                ->whereYear('sale_time', $year)
                ->sum('amount');

            $orders = Order::where('distributor_id', $distributorId)
                ->whereYear('sale_time', $year)
                ->count();

            $stats[$year] = [
                'sales' => $sales,
                'orders' => $orders,
                'average' => $orders > 0 ? $sales / $orders : 0
            ];
        }

        return $stats;
    }

    /**
     * Lấy top performers trong network
     */
    private function getTopPerformers($distributorId): array
    {
        $distributor = Distributor::find($distributorId);
        $descendantIds = $distributor->getAllDescendantIds();
        $descendantIds->push($distributorId);

        return Order::with('distributor')
            ->whereIn('distributor_id', $descendantIds)
            ->selectRaw('distributor_id, COUNT(*) as order_count, SUM(amount) as total_sales')
            ->groupBy('distributor_id')
            ->orderByDesc('total_sales')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                $item->distributor->total_sales = $item->total_sales;
                $item->distributor->order_count = $item->order_count;
                return $item->distributor;
            })
            ->toArray();
    }
}
