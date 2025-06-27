@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng #' . $order->id)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Đơn hàng</a></li>
<li class="breadcrumb-item active">Chi tiết</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Chi tiết đơn hàng #{{ $order->id }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Chỉnh sửa
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Thông tin đơn hàng -->
                        <div class="col-md-7">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Thông tin đơn hàng</h3>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th style="width: 200px;">ID đơn hàng</th>
                                                <td><strong>{{ $order->id }}</strong></td>
                                            </tr>
                                            <tr>
                                                <th>Mã hóa đơn</th>
                                                <td>
                                                    <span class="badge badge-info" style="font-size: 14px;">
                                                        {{ $order->bill_code }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Số tiền</th>
                                                <td>
                                                    <h4 class="text-success mb-0">
                                                        {{ number_format($order->amount) }} VNĐ
                                                    </h4>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Thời gian bán</th>
                                                <td>{{ $order->sale_time->format('d/m/Y H:i:s') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Cấp độ distributor</th>
                                                <td>
                                                    <span class="badge badge-info">F{{ $order->distributor_level }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Ghi chú</th>
                                                <td>
                                                    @if($order->notes)
                                                        {{ $order->notes }}
                                                    @else
                                                        <span class="text-muted">Không có ghi chú</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Ngày tạo</th>
                                                <td>{{ $order->created_at->format('d/m/Y H:i:s') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Cập nhật lần cuối</th>
                                                <td>{{ $order->updated_at->format('d/m/Y H:i:s') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Thông tin distributor -->
                        <div class="col-md-5">
                            <div class="card card-outline card-info">
                                <div class="card-header">
                                    <h3 class="card-title">Thông tin Distributor</h3>
                                </div>
                                <div class="card-body">
                                    @if($order->distributor)
                                        <div class="text-center mb-3">
                                            <i class="fas fa-user-circle fa-3x text-info"></i>
                                        </div>
                                        <table class="table table-sm">
                                            <tbody>
                                                <tr>
                                                    <th>Tên:</th>
                                                    <td>
                                                        <a href="{{ route('distributors.show', $order->distributor->id) }}">
                                                            {{ $order->distributor->distributor_name }}
                                                        </a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Mã:</th>
                                                    <td>{{ $order->distributor->distributor_code }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Email:</th>
                                                    <td>{{ $order->distributor->distributor_email }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Điện thoại:</th>
                                                    <td>{{ $order->distributor->distributor_phone }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Cấp độ:</th>
                                                    <td>
                                                        <span class="badge badge-info">F{{ $order->distributor->level }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Trạng thái:</th>
                                                    <td>
                                                        @if($order->distributor->status == 'active')
                                                            <span class="badge badge-success">Hoạt động</span>
                                                        @elseif($order->distributor->status == 'inactive')
                                                            <span class="badge badge-secondary">Không hoạt động</span>
                                                        @else
                                                            <span class="badge badge-warning">Tạm ngưng</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="text-center text-muted">
                                            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                            <p>Distributor không tồn tại hoặc đã bị xóa</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Thống kê nhanh -->
                            <div class="card card-outline card-success">
                                <div class="card-header">
                                    <h3 class="card-title">Thống kê nhanh</h3>
                                </div>
                                <div class="card-body">
                                    @if($order->distributor)
                                        @php
                                            $distributorOrders = $order->distributor->orders;
                                            $totalSales = $distributorOrders->sum('amount');
                                            $totalOrders = $distributorOrders->count();
                                            $averageOrder = $totalOrders > 0 ? $totalSales / $totalOrders : 0;
                                        @endphp
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <div class="info-box bg-light">
                                                    <span class="info-box-icon bg-info">
                                                        <i class="fas fa-shopping-cart"></i>
                                                    </span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Số đơn hàng</span>
                                                        <span class="info-box-number">{{ $totalOrders }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="info-box bg-light">
                                                    <span class="info-box-icon bg-success">
                                                        <i class="fas fa-dollar-sign"></i>
                                                    </span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Doanh số</span>
                                                        <span class="info-box-number">{{ number_format($totalSales) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-center text-muted">
                                            <p>Không có thống kê</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Các đơn hàng khác của distributor -->
                    @if($order->distributor)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card card-outline card-warning">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            Các đơn hàng khác của {{ $order->distributor->distributor_name }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        @php
                                            $otherOrders = $order->distributor->orders()
                                                ->where('id', '!=', $order->id)
                                                ->latest('sale_time')
                                                ->take(5)
                                                ->get();
                                        @endphp

                                        @if($otherOrders->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Mã hóa đơn</th>
                                                            <th>Số tiền</th>
                                                            <th>Thời gian</th>
                                                            <th>Thao tác</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($otherOrders as $otherOrder)
                                                        <tr>
                                                            <td>{{ $otherOrder->id }}</td>
                                                            <td>{{ $otherOrder->bill_code }}</td>
                                                            <td>{{ number_format($otherOrder->amount) }} VNĐ</td>
                                                            <td>{{ $otherOrder->sale_time->format('d/m/Y H:i') }}</td>
                                                            <td>
                                                                <a href="{{ route('orders.show', $otherOrder->id) }}"
                                                                    class="btn btn-sm btn-info">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center text-muted">
                                                <p>Không có đơn hàng nào khác</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
