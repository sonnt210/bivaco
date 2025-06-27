@extends('layouts.app')

@section('title', 'Tạo mới Distributor')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('distributors.index') }}">Tạo nhà phân phối</a></li>
<li class="breadcrumb-item active">Tạo mới</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-plus-circle text-primary"></i>
                        Tạo mới nhà phân phối
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('distributors.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('distributors.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="distributor_code">
                                        <i class="fas fa-barcode"></i> Mã nhà phân phối <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control @error('distributor_code') is-invalid @enderror"
                                           id="distributor_code"
                                           name="distributor_code"
                                           value="{{ old('distributor_code') }}"
                                           placeholder="VD: DIST001"
                                           required>
                                    @error('distributor_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="distributor_name">
                                        <i class="fas fa-user"></i> Tên nhà phân phối <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control @error('distributor_name') is-invalid @enderror"
                                           id="distributor_name"
                                           name="distributor_name"
                                           value="{{ old('distributor_name') }}"
                                           placeholder="Nhập tên đầy đủ"
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
                                           value="{{ old('distributor_email') }}"
                                           placeholder="example@email.com"
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
                                           value="{{ old('distributor_phone') }}"
                                           placeholder="0123456789"
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
                                      placeholder="Nhập địa chỉ đầy đủ"
                                      required>{{ old('distributor_address') }}</textarea>
                            @error('distributor_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="parent_id">
                                        <i class="fas fa-users"></i> Phụ thuộc nhà phân phối
                                    </label>
                                    <select class="form-control @error('parent_id') is-invalid @enderror"
                                            id="parent_id"
                                            name="parent_id">
                                        <option value="">-- Chọn nhà phân phối cha (tùy chọn) --</option>
                                        @foreach($parents as $parent)
                                            <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                                {{ $parent->distributor_name }} ({{ $parent->distributor_code }}) - F{{ $parent->level }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('parent_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="join_date">
                                        <i class="fas fa-calendar"></i> Ngày tham gia <span class="text-danger">*</span>
                                    </label>
                                    <input type="date"
                                           class="form-control @error('join_date') is-invalid @enderror"
                                           id="join_date"
                                           name="join_date"
                                           value="{{ old('join_date', date('Y-m-d')) }}"
                                           required>
                                    @error('join_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Tạo nhà phân phối
                            </button>
                            <a href="{{ route('distributors.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Hủy
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
