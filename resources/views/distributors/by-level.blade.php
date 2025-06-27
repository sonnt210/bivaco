@extends('layouts.app')

@section('title', 'Distributors ' . $levelName)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('distributors.index') }}">Distributors</a></li>
<li class="breadcrumb-item active">{{ $levelName }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-layer-group text-primary"></i>
                        Distributors {{ $levelName }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('distributors.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ $levelDescription }}</p>

                    <!-- Thống kê cấp độ -->
                    @if($currentLevelStats)
                    <div class="row mb-4">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $currentLevelStats['count'] }}</h3>
                                    <p>Tổng số</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $currentLevelStats['active_count'] ?? 0 }}</h3>
                                    <p>Đang hoạt động</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-user-check"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ number_format($currentLevelStats['total_sales'] ?? 0) }}</h3>
                                    <p>Doanh số (VNĐ)</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>{{ $currentLevelStats['total_orders'] ?? 0 }}</h3>
                                    <p>Tổng đơn hàng</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if(count($distributors) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Thông tin</th>
                                        <th>Email</th>
                                        <th>Số điện thoại</th>
                                        <th>Ngày tham gia</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($distributors as $distributor)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-3">
                                                        <i class="fas fa-user text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $distributor->distributor_name }}</h6>
                                                        <small class="text-muted">{{ $distributor->distributor_code }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $distributor->distributor_email }}</td>
                                            <td>{{ $distributor->distributor_phone }}</td>
                                            <td>{{ $distributor->join_date->format('d/m/Y') }}</td>
                                            <td>
                                                @if($distributor->status == 'active')
                                                    <span class="badge badge-success">Hoạt động</span>
                                                @elseif($distributor->status == 'inactive')
                                                    <span class="badge badge-secondary">Không hoạt động</span>
                                                @else
                                                    <span class="badge badge-warning">Tạm ngưng</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('distributors.show', $distributor->id) }}"
                                                       class="btn btn-sm btn-info" title="Xem chi tiết">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('distributors.income-details', $distributor->id) }}"
                                                       class="btn btn-sm btn-success" title="Xem doanh số">
                                                        <i class="fas fa-chart-line"></i>
                                                    </a>
                                                    <a href="{{ route('distributors.edit', $distributor->id) }}"
                                                       class="btn btn-sm btn-warning" title="Chỉnh sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Phân trang -->
                        <div class="d-flex justify-content-center">
                            {{ $distributors->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Chưa có distributor nào ở cấp độ {{ $levelName }}</h5>
                            <p class="text-muted">Hãy tạo distributor mới để bắt đầu</p>
                            <a href="{{ route('distributors.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tạo Distributor mới
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 