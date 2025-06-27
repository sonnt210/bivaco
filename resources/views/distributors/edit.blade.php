@extends('layouts.app')

@section('title', 'Chỉnh sửa Distributor - ' . $distributor->distributor_name)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('distributors.index') }}">Distributors</a></li>
<li class="breadcrumb-item active">Chỉnh sửa</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-edit text-primary"></i>
                        Chỉnh sửa Distributor - {{ $distributor->distributor_name }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('distributors.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Thông tin hiện tại -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card card-outline card-warning">
                                <div class="card-header">
                                    <h3 class="card-title">Thông tin hiện tại</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>ID:</strong>
                                            <div class="text-muted">{{ $distributor->id }}</div>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Mã:</strong>
                                            <div class="text-muted">{{ $distributor->distributor_code }}</div>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Cấp độ:</strong>
                                            <div class="text-muted">
                                                <span class="badge badge-info">F{{ $distributor->level }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Trạng thái:</strong>
                                            <div class="text-muted">
                                                @if($distributor->status == 'active')
                                                    <span class="badge badge-success">Hoạt động</span>
                                                @elseif($distributor->status == 'inactive')
                                                    <span class="badge badge-secondary">Không hoạt động</span>
                                                @else
                                                    <span class="badge badge-warning">Tạm ngưng</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Thông tin distributor cha -->
                    @if($distributor->parent)
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card card-outline card-success">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-user-tie"></i>
                                        Trực thuộc nhà phân phối
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong>Tên:</strong>
                                            <div class="text-muted">{{ $distributor->parent->distributor_name }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Mã:</strong>
                                            <div class="text-muted">{{ $distributor->parent->distributor_code }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Cấp độ:</strong>
                                            <div class="text-muted">
                                                <span class="badge badge-info">F{{ $distributor->parent->level }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <form action="{{ route('distributors.update', $distributor->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="distributor_code">
                                        <i class="fas fa-barcode"></i> Mã Distributor <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control @error('distributor_code') is-invalid @enderror"
                                           id="distributor_code"
                                           name="distributor_code"
                                           value="{{ old('distributor_code', $distributor->distributor_code) }}"
                                           required>
                                    @error('distributor_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="distributor_name">
                                        <i class="fas fa-user"></i> Tên Distributor <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control @error('distributor_name') is-invalid @enderror"
                                           id="distributor_name"
                                           name="distributor_name"
                                           value="{{ old('distributor_name', $distributor->distributor_name) }}"
                                           required>
                                    @error('distributor_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="distributor_email">
                                        <i class="fas fa-envelope"></i> Email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email"
                                           class="form-control @error('distributor_email') is-invalid @enderror"
                                           id="distributor_email"
                                           name="distributor_email"
                                           value="{{ old('distributor_email', $distributor->distributor_email) }}"
                                           required>
                                    @error('distributor_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="distributor_phone">
                                        <i class="fas fa-phone"></i> Số điện thoại <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control @error('distributor_phone') is-invalid @enderror"
                                           id="distributor_phone"
                                           name="distributor_phone"
                                           value="{{ old('distributor_phone', $distributor->distributor_phone) }}"
                                           required>
                                    @error('distributor_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="distributor_address">
                                <i class="fas fa-map-marker-alt"></i> Địa chỉ <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('distributor_address') is-invalid @enderror"
                                      id="distributor_address"
                                      name="distributor_address"
                                      rows="3"
                                      required>{{ old('distributor_address', $distributor->distributor_address) }}</textarea>
                            @error('distributor_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="parent_id">
                                        <i class="fas fa-users"></i> Distributor cha
                                    </label>
                                    <select class="form-control @error('parent_id') is-invalid @enderror"
                                            id="parent_id"
                                            name="parent_id">
                                        <option value="">-- Chọn Distributor cha (tùy chọn) --</option>
                                        @foreach($parents as $parent)
                                            <option value="{{ $parent->id }}" 
                                                {{ old('parent_id', $distributor->parent_id) == $parent->id ? 'selected' : '' }}
                                                data-level="{{ $parent->level }}">
                                                {{ $parent->distributor_name }} ({{ $parent->distributor_code }}) - F{{ $parent->level }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('parent_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="join_date">
                                        <i class="fas fa-calendar"></i> Ngày tham gia <span class="text-danger">*</span>
                                    </label>
                                    <input type="date"
                                           class="form-control @error('join_date') is-invalid @enderror"
                                           id="join_date"
                                           name="join_date"
                                           value="{{ old('join_date', $distributor->join_date->format('Y-m-d')) }}"
                                           required>
                                    @error('join_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="status">
                                        <i class="fas fa-toggle-on"></i> Trạng thái <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control @error('status') is-invalid @enderror"
                                            id="status"
                                            name="status"
                                            required>
                                        <option value="active" {{ old('status', $distributor->status) == 'active' ? 'selected' : '' }}>
                                            Hoạt động
                                        </option>
                                        <option value="inactive" {{ old('status', $distributor->status) == 'inactive' ? 'selected' : '' }}>
                                            Không hoạt động
                                        </option>
                                        <option value="suspended" {{ old('status', $distributor->status) == 'suspended' ? 'selected' : '' }}>
                                            Tạm ngưng
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Thông tin preview -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-outline card-info">
                                    <div class="card-header">
                                        <h3 class="card-title">Thông tin sau khi cập nhật</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>Mã:</strong>
                                                <div id="preview_code" class="text-muted">{{ $distributor->distributor_code }}</div>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Tên:</strong>
                                                <div id="preview_name" class="text-muted">{{ $distributor->distributor_name }}</div>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Email:</strong>
                                                <div id="preview_email" class="text-muted">{{ $distributor->distributor_email }}</div>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Cấp độ:</strong>
                                                <div id="preview_level" class="text-muted">F{{ $distributor->level }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cập nhật Distributor
                            </button>
                            <a href="{{ route('distributors.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                            <a href="{{ route('distributors.show', $distributor->id) }}" class="btn btn-info">
                                <i class="fas fa-eye"></i> Xem chi tiết
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Cập nhật preview khi thay đổi mã
    $('#distributor_code').on('input', function() {
        var code = $(this).val();
        $('#preview_code').text(code || 'Chưa nhập');
    });

    // Cập nhật preview khi thay đổi tên
    $('#distributor_name').on('input', function() {
        var name = $(this).val();
        $('#preview_name').text(name || 'Chưa nhập');
    });

    // Cập nhật preview khi thay đổi email
    $('#distributor_email').on('input', function() {
        var email = $(this).val();
        $('#preview_email').text(email || 'Chưa nhập');
    });

    // Cập nhật preview khi thay đổi distributor cha
    $('#parent_id').change(function() {
        var selectedOption = $(this).find('option:selected');
        var level = 1;
        
        if (selectedOption.val()) {
            var levelText = selectedOption.text();
            var levelMatch = levelText.match(/F(\d+)/);
            if (levelMatch) {
                level = parseInt(levelMatch[1]) + 1;
            }
        }
        
        $('#preview_level').text('F' + level);
    });

    // Khởi tạo preview ban đầu
    $('#distributor_code').trigger('input');
    $('#distributor_name').trigger('input');
    $('#distributor_email').trigger('input');
    $('#parent_id').trigger('change');
});
</script>
@endpush
