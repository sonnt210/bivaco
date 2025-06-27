@extends('layouts.app')

@section('title', 'Quản lý đơn hàng')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quản lý đơn hàng</h3>
                    <div class="card-tools">
                        <a href="{{ route('orders.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Tạo đơn hàng mới
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Bộ lọc -->
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Bộ lọc</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('orders.index') }}">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Distributor</label>
                                            <select name="user_id" class="form-control">
                                                <option value="">Tất cả Distributors</option>
                                                @foreach($distributors as $distributor)
                                                    <option value="{{ $distributor->id }}"
                                                        {{ request('user_id') == $distributor->id ? 'selected' : '' }}>
                                                        {{ $distributor->distributor_code }} - {{ $distributor->distributor_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Cấp độ</label>
                                            <select name="user_level" class="form-control">
                                                <option value="">Tất cả cấp độ</option>
                                                @foreach($distributorLevels as $level)
                                                    <option value="{{ $level }}"
                                                        {{ request('user_level') == $level ? 'selected' : '' }}>
                                                        F{{ $level }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Từ ngày</label>
                                            <input type="date" name="start_date" class="form-control"
                                                value="{{ request('start_date') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Đến ngày</label>
                                            <input type="date" name="end_date" class="form-control"
                                                value="{{ request('end_date') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Mã hóa đơn</label>
                                            <input type="text" name="bill_code" class="form-control"
                                                placeholder="Nhập mã hóa đơn" value="{{ request('bill_code') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Giá từ</label>
                                            <input type="number" name="min_amount" class="form-control"
                                                placeholder="0" value="{{ request('min_amount') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Giá đến</label>
                                            <input type="number" name="max_amount" class="form-control"
                                                placeholder="0" value="{{ request('max_amount') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <div>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-search"></i> Tìm kiếm
                                                </button>
                                                <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                                                    <i class="fas fa-times"></i> Xóa bộ lọc
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Bảng danh sách -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Mã hóa đơn</th>
                                    <th>Distributor</th>
                                    <th>Cấp độ</th>
                                    <th>Số tiền</th>
                                    <th>Thời gian</th>
                                    <th>Ghi chú</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>
                                        <strong>{{ $order->bill_code }}</strong>
                                    </td>
                                    <td>
                                        @if($order->distributor)
                                            <a href="{{ route('distributors.show', $order->distributor->id) }}">
                                                {{ $order->distributor->distributor_name }}
                                            </a>
                                            <br>
                                            <small class="text-muted">{{ $order->distributor->distributor_code }}</small>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-info">F{{ $order->user_level }}</span>
                                    </td>
                                    <td>
                                        <strong class="text-success">{{ number_format($order->amount) }} VNĐ</strong>
                                    </td>
                                    <td>
                                        {{ $order->sale_time->format('d/m/Y H:i') }}
                                    </td>
                                    <td>
                                        @if($order->notes)
                                            <span title="{{ $order->notes }}">
                                                {{ Str::limit($order->notes, 30) }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('orders.show', $order->id) }}"
                                                class="btn btn-sm btn-info" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('orders.edit', $order->id) }}"
                                                class="btn btn-sm btn-warning" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('orders.destroy', $order->id) }}"
                                                method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này?')"
                                                    title="Xóa">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">
                                        <p class="text-muted">Không có đơn hàng nào</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Phân trang -->
                    <div class="d-flex justify-content-center">
                        {{ $orders->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto submit form khi thay đổi select
    $('select[name="user_id"], select[name="user_level"]').change(function() {
        $(this).closest('form').submit();
    });
});
</script>
@endpush
