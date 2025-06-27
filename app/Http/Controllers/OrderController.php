<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class OrderController extends Controller
{
    /**
     * Hiển thị danh sách tất cả orders
     */
    public function index(Request $request): View
    {
        $query = Order::with(['user']);

        // Lọc theo distributor
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Lọc theo cấp độ distributor
        if ($request->has('user_level') && $request->user_level) {
            $query->where('user_level', $request->user_level);
        }

        // Lọc theo khoảng thời gian
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('sale_time', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('sale_time', '<=', $request->end_date);
        }

        // Lọc theo khoảng giá
        if ($request->has('min_amount') && $request->min_amount) {
            $query->where('amount', '>=', $request->min_amount);
        }

        if ($request->has('max_amount') && $request->max_amount) {
            $query->where('amount', '<=', $request->max_amount);
        }

        // Tìm kiếm theo bill_code
        if ($request->has('bill_code') && $request->bill_code) {
            $query->where('bill_code', 'like', '%' . $request->bill_code . '%');
        }

        $orders = $query->latest('sale_time')->paginate(20);

        // Lấy danh sách distributors cho filter
        $distributors = User::whereNotNull('distributor_code')->where('status', 'active')->get();
        $distributorLevels = User::getAllLevels();

        return view('orders.index', compact(
            'orders',
            'distributors',
            'distributorLevels',
        ));
    }

    /**
     * Hiển thị orders theo distributor
     */
    public function byDistributor($distributorId): View
    {
        $distributor = User::whereNotNull('distributor_code')->findOrFail($distributorId);
        $orders = Order::where('user_id', $distributorId)
            ->latest('sale_time')
            ->paginate(20);

        $totalSales = $orders->sum('amount');
        $totalOrders = $orders->count();
        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        return view('orders.by-distributor', compact(
            'distributor',
            'orders',
            'totalSales',
            'totalOrders',
            'averageOrderValue'
        ));
    }

    /**
     * Hiển thị orders theo cấp độ distributor
     */
    public function byLevel($level): View
    {
        $orders = Order::where('user_level', $level)
            ->with('user')
            ->latest('sale_time')
            ->paginate(20);

        $levelName = 'Level ' . $level;
        $levelDescription = User::getLevelDescription($level);

        $totalSales = $orders->sum('amount');
        $totalOrders = $orders->count();
        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        return view('orders.by-level', compact(
            'orders',
            'level',
            'levelName',
            'levelDescription',
            'totalSales',
            'totalOrders',
            'averageOrderValue'
        ));
    }

    /**
     * Hiển thị chi tiết order
     */
    public function show($id): View
    {
        $order = Order::with(['user'])->findOrFail($id);

        return view('orders.show', compact('order'));
    }

    /**
     * Form tạo mới order
     */
    public function create(): View
    {
        $distributors = User::whereNotNull('distributor_code')->where('status', 'active')->get();

        return view('orders.create', compact('distributors'));
    }

    /**
     * Lưu order mới
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'sale_time' => 'required|date',
            'bill_code' => 'required|unique:orders',
            'notes' => 'nullable|string'
        ]);

        // Lấy thông tin distributor để set level
        $distributor = User::find($request->user_id);
        $distributorLevel = $distributor->level;

        Order::create([
            'user_id' => $request->user_id,
            'user_level' => $distributorLevel,
            'amount' => $request->amount,
            'sale_time' => $request->sale_time,
            'bill_code' => $request->bill_code,
            'notes' => $request->notes
        ]);

        return redirect()->route('orders.index')
            ->with('success', 'Đơn hàng đã được tạo thành công!');
    }

    /**
     * Form chỉnh sửa order
     */
    public function edit($id): View
    {
        $order = Order::findOrFail($id);
        $distributors = User::whereNotNull('distributor_code')->where('status', 'active')->get();

        return view('orders.edit', compact('order', 'distributors'));
    }

    /**
     * Cập nhật order
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'sale_time' => 'required|date',
            'bill_code' => 'required|unique:orders,bill_code,' . $id,
            'notes' => 'nullable|string'
        ]);

        // Lấy thông tin distributor để set level
        $distributor = User::find($request->user_id);
        $distributorLevel = $distributor->level;

        $order->update([
            'user_id' => $request->user_id,
            'user_level' => $distributorLevel,
            'amount' => $request->amount,
            'sale_time' => $request->sale_time,
            'bill_code' => $request->bill_code,
            'notes' => $request->notes
        ]);

        return redirect()->route('orders.show', $id)
            ->with('success', 'Đơn hàng đã được cập nhật thành công!');
    }

    /**
     * Xóa order
     */
    public function destroy($id): RedirectResponse
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return redirect()->route('orders.index')
            ->with('success', 'Đơn hàng đã được xóa thành công!');
    }

    /**
     * Tìm kiếm orders
     */
    public function search(Request $request): View
    {
        $query = $request->get('q');
        
        $orders = Order::with('user')
            ->where(function($q) use ($query) {
                $q->where('bill_code', 'like', "%{$query}%")
                  ->orWhere('notes', 'like', "%{$query}%")
                  ->orWhereHas('user', function($userQuery) use ($query) {
                      $userQuery->where('distributor_name', 'like', "%{$query}%")
                               ->orWhere('distributor_code', 'like', "%{$query}%");
                  });
            })
            ->latest('sale_time')
            ->paginate(20);

        return view('orders.search', compact('orders', 'query'));
    }
}
