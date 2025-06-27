@extends('layouts.app')

@section('title', 'Thông tin Doanh số & Thu nhập - ' . $distributor->distributor_name)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('distributors.index') }}">Distributors</a></li>
<li class="breadcrumb-item"><a href="{{ route('distributors.show', $distributor->id) }}">{{ $distributor->distributor_name }}</a></li>
<li class="breadcrumb-item active">Doanh số</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line text-primary"></i>
                        Thông tin Doanh số & Thu nhập - {{ $distributor->distributor_name }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('distributors.show', $distributor->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Thông tin distributor -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <h5>{{ $distributor->distributor_name }} ({{ $distributor->distributor_code }})</h5>
                            <p class="text-muted mb-0">{{ $distributor->distributor_email }}</p>
                        </div>
                        <div class="col-md-4 text-right">
                            <span class="badge badge-primary">F{{ $levelName }}</span>
                        </div>
                    </div>

                    <!-- Thưởng đồng chia -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card card-outline card-warning">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-gift"></i>
                                        Thưởng đồng chia - Tháng {{ \Carbon\Carbon::createFromFormat('Y-m', $currentMonth)->format('m/Y') }}
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <!-- Thống kê thưởng -->
                                    <div class="row mb-4">
                                        <div class="col-lg-3 col-6">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-info">
                                                    <i class="fas fa-user"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Doanh số cá nhân (T)</span>
                                                    <span class="info-box-number">{{ number_format($bonusInfo['personal_sales']) }} VNĐ</span>
                                                    <div class="progress">
                                                        <div class="progress-bar" style="width: {{ $bonusInfo['personal_sales'] >= 5000000 ? 100 : ($bonusInfo['personal_sales'] / 5000000 * 100) }}%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-6">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-{{ $bonusInfo['is_qualified'] ? 'success' : 'danger' }}">
                                                    <i class="fas fa-trophy"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Trạng thái thưởng</span>
                                                    <span class="info-box-number">
                                                        {{ $bonusInfo['is_qualified'] ? 'Đủ điều kiện' : 'Không đủ điều kiện' }}
                                                    </span>
                                                    <div class="progress">
                                                        <div class="progress-bar" style="width: {{ $bonusInfo['is_qualified'] ? 100 : 0 }}%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-6">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-warning">
                                                    <i class="fas fa-money-bill-wave"></i>
                                                </span>
                                                <div class="info-box-content">
                                                    <span class="info-box-text">Thưởng nhận được</span>
                                                    <span class="info-box-number">{{ number_format($bonusInfo['bonus_amount']) }} VNĐ</span>
                                                    <div class="progress">
                                                        <div class="progress-bar" style="width: 100%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Chi tiết điều kiện -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card card-outline card-primary">
                                                <div class="card-header">
                                                    <h3 class="card-title">
                                                        <i class="fas fa-user-check"></i>
                                                        Điều kiện doanh số cá nhân
                                                    </h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <strong>Tháng T:</strong>
                                                            <div class="text-muted">{{ number_format($bonusInfo['personal_sales']) }} VNĐ</div>
                                                            <span class="badge badge-{{ $bonusInfo['personal_sales'] >= 5000000 ? 'success' : 'danger' }}">
                                                                {{ $bonusInfo['personal_sales'] >= 5000000 ? 'Đạt' : 'Không đạt' }}
                                                            </span>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <strong>Tháng T-1:</strong>
                                                            <div class="text-muted">{{ number_format($bonusInfo['personal_sales_t1']) }} VNĐ</div>
                                                            <span class="badge badge-{{ $bonusInfo['personal_sales_t1'] >= 5000000 ? 'success' : 'danger' }}">
                                                                {{ $bonusInfo['personal_sales_t1'] >= 5000000 ? 'Đạt' : 'Không đạt' }}
                                                            </span>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <strong>Tháng T-2:</strong>
                                                            <div class="text-muted">{{ number_format($bonusInfo['personal_sales_t2']) }} VNĐ</div>
                                                            <span class="badge badge-{{ $bonusInfo['personal_sales_t2'] >= 5000000 ? 'success' : 'danger' }}">
                                                                {{ $bonusInfo['personal_sales_t2'] >= 5000000 ? 'Đạt' : 'Không đạt' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="text-center">
                                                        <strong>Kết quả:</strong>
                                                        <span class="badge badge-{{ $bonusInfo['personal_condition_met'] ? 'success' : 'danger' }} ml-2">
                                                            {{ $bonusInfo['personal_condition_met'] ? 'Đạt điều kiện' : 'Không đạt điều kiện' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card card-outline card-success">
                                                <div class="card-header">
                                                    <h3 class="card-title">
                                                        <i class="fas fa-sitemap"></i>
                                                        Điều kiện doanh số nhánh
                                                    </h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <strong>Tháng T:</strong>
                                                            <div class="text-muted">{{ $bonusInfo['qualified_branches'] }} nhánh</div>
                                                            <span class="badge badge-{{ $bonusInfo['qualified_branches'] >= 2 ? 'success' : 'danger' }}">
                                                                {{ $bonusInfo['qualified_branches'] >= 2 ? 'Đạt' : 'Không đạt' }}
                                                            </span>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <strong>Tháng T-1:</strong>
                                                            <div class="text-muted">{{ $bonusInfo['qualified_branches_t1'] }} nhánh</div>
                                                            <span class="badge badge-{{ $bonusInfo['qualified_branches_t1'] >= 2 ? 'success' : 'danger' }}">
                                                                {{ $bonusInfo['qualified_branches_t1'] >= 2 ? 'Đạt' : 'Không đạt' }}
                                                            </span>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <strong>Tháng T-2:</strong>
                                                            <div class="text-muted">{{ $bonusInfo['qualified_branches_t2'] }} nhánh</div>
                                                            <span class="badge badge-{{ $bonusInfo['qualified_branches_t2'] >= 2 ? 'success' : 'danger' }}">
                                                                {{ $bonusInfo['qualified_branches_t2'] >= 2 ? 'Đạt' : 'Không đạt' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="text-center">
                                                        <strong>Kết quả:</strong>
                                                        <span class="badge badge-{{ $bonusInfo['branch_condition_met'] ? 'success' : 'danger' }} ml-2">
                                                            {{ $bonusInfo['branch_condition_met'] ? 'Đạt điều kiện' : 'Không đạt điều kiện' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lịch sử thưởng -->
                    @if($bonusHistory->count() > 0)
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card card-outline card-info">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-history"></i>
                                            Lịch sử thưởng đồng chia (12 tháng gần nhất)
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Tháng</th>
                                                        <th>Doanh số cá nhân</th>
                                                        <th>Nhánh đạt chuẩn</th>
                                                        <th>Đủ điều kiện</th>
                                                        <th>Thưởng</th>
                                                        <th>Ghi chú</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($bonusHistory as $record)
                                                        <tr>
                                                            <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $record->month_year)->format('m/Y') }}</td>
                                                            <td>{{ number_format($record->personal_sales) }} VNĐ</td>
                                                            <td>{{ $record->qualified_branches }}</td>
                                                            <td>
                                                                <span class="badge badge-{{ $record->is_qualified ? 'success' : 'danger' }}">
                                                                    {{ $record->is_qualified ? 'Có' : 'Không' }}
                                                                </span>
                                                            </td>
                                                            <td>{{ number_format($record->bonus_amount) }} VNĐ</td>
                                                            <td>
                                                                <small class="text-muted">{{ Str::limit($record->notes, 50) }}</small>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Thống kê con trực tiếp -->
                    @if(count($childrenStats) > 0)
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-users"></i>
                                        Thống kê con trực tiếp
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Distributor</th>
                                                    <th>Doanh số cá nhân</th>
                                                    <th>Doanh số network</th>
                                                    <th>Số đơn hàng</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($childrenStats as $stat)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('distributors.show', $stat['distributor']->id) }}">
                                                            {{ $stat['distributor']->distributor_name }}
                                                        </a>
                                                        <br>
                                                        <small class="text-muted">{{ $stat['distributor']->distributor_code }}</small>
                                                    </td>
                                                    <td>{{ number_format($stat['own_sales']) }} VNĐ</td>
                                                    <td>{{ number_format($stat['network_sales']) }} VNĐ</td>
                                                    <td>{{ $stat['own_orders'] }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
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
