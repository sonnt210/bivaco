@extends('layouts.app')

@section('title', 'Quản lý nhà phân phối')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users text-primary"></i>
                        Quản lý nhà phân phối
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('distributors.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Thêm nhà phân phối
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Thống kê tổng quan -->
                    <div class="row mb-4">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ number_format($totalDistributors) }}</h3>
                                    <p>Tổng số nhà phân phối</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ count($allLevels) }}</h3>
                                    <p>Cấp độ hiện có</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-layer-group"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>{{ count($recentDistributors) }}</h3>
                                    <p>Mới thêm gần đây</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Distributors -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-outline card-success">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-clock"></i>
                                        Danh sách nhà phân phối
                                    </h3>
                                </div>
                                <div class="card-body">
                                    @if(count($recentDistributors) > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Thông tin</th>
                                                        <th>Cấp độ</th>
                                                        <th>Email</th>
                                                        <th>Số điện thoại</th>
                                                        <th>Ngày tham gia</th>
                                                        <th>Trạng thái</th>
                                                        <th>Thao tác</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($recentDistributors as $distributor)
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
                                                            <td>
                                                                <span class="badge badge-info">F{{ $distributor->level }}</span>
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
                                                                    <a href="{{ route('distributors.income-details', $distributor->id) }}"
                                                                        class="btn btn-sm btn-success" title="Xem doanh số">
                                                                        <i class="fas fa-chart-line"></i>
                                                                    </a>
                                                                    <a href="{{ route('distributors.edit', $distributor->id) }}"
                                                                        class="btn btn-sm btn-warning" title="Chỉnh sửa">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                    <form action="{{ route('distributors.destroy', $distributor->id) }}"
                                                                        method="POST" style="display:inline-block;"
                                                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Chưa có distributor nào</h5>
                                            <p class="text-muted">Hãy tạo distributor đầu tiên để bắt đầu</p>
                                            <a href="{{ route('distributors.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus"></i> Tạo Distributor đầu tiên
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
