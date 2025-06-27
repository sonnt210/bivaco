@extends('layouts.app')

@section('title', 'Tạo đơn hàng mới')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tạo đơn hàng mới</h3>
                    <div class="card-tools">
                        <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('orders.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_id">Distributor <span class="text-danger">*</span></label>
                                    <select name="user_id" id="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
                                        <option value="">Chọn Distributor</option>
                                        @foreach($distributors as $distributor)
                                            <option value="{{ $distributor->id }}"
                                                {{ old('user_id') == $distributor->id ? 'selected' : '' }}>
                                                {{ $distributor->distributor_code }} - {{ $distributor->distributor_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bill_code">Mã hóa đơn <span class="text-danger">*</span></label>
                                    <input type="text" name="bill_code" id="bill_code"
                                        class="form-control @error('bill_code') is-invalid @enderror"
                                        value="{{ old('bill_code') }}"
                                        placeholder="Nhập mã hóa đơn" required>
                                    @error('bill_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="amount">Số tiền <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="amount" id="amount"
                                            class="form-control @error('amount') is-invalid @enderror"
                                            value="{{ old('amount') }}"
                                            placeholder="Nhập số tiền"
                                            min="0" step="0.01" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">VNĐ</span>
                                        </div>
                                    </div>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sale_time">Thời gian bán <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="sale_time" id="sale_time"
                                        class="form-control @error('sale_time') is-invalid @enderror"
                                        value="{{ old('sale_time', now()->format('Y-m-d\TH:i')) }}" required>
                                    @error('sale_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="notes">Ghi chú</label>
                                    <textarea name="notes" id="notes" rows="3"
                                        class="form-control @error('notes') is-invalid @enderror"
                                        placeholder="Nhập ghi chú (nếu có)">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Tạo đơn hàng
                                    </button>
                                    <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Hủy
                                    </a>
                                </div>
                            </div>
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
    // Cập nhật preview khi thay đổi distributor
    $('#user_id').change(function() {
        var selectedOption = $(this).find('option:selected');
        var distributorName = selectedOption.text();
        var level = selectedOption.data('level');

        $('#preview_distributor').text(distributorName || 'Chưa chọn');
        $('#preview_level').text(level ? 'F' + level : '-');
    });

    // Cập nhật preview khi thay đổi số tiền
    $('#amount').on('input', function() {
        var amount = $(this).val();
        if (amount) {
            $('#preview_amount').text(new Intl.NumberFormat('vi-VN').format(amount) + ' VNĐ');
        } else {
            $('#preview_amount').text('0 VNĐ');
        }
    });

    // Cập nhật preview khi thay đổi thời gian
    $('#sale_time').change(function() {
        var time = $(this).val();
        if (time) {
            var date = new Date(time);
            $('#preview_time').text(date.toLocaleString('vi-VN'));
        } else {
            $('#preview_time').text('-');
        }
    });

    // Tự động tạo mã hóa đơn nếu để trống
    $('#bill_code').on('blur', function() {
        if (!$(this).val()) {
            var now = new Date();
            var billCode = 'BILL' + now.getFullYear() +
                          String(now.getMonth() + 1).padStart(2, '0') +
                          String(now.getDate()).padStart(2, '0') +
                          String(now.getHours()).padStart(2, '0') +
                          String(now.getMinutes()).padStart(2, '0') +
                          String(now.getSeconds()).padStart(2, '0');
            $(this).val(billCode);
        }
    });

    // Khởi tạo preview ban đầu
    $('#user_id').trigger('change');
    $('#amount').trigger('input');
    $('#sale_time').trigger('change');
});
</script>
@endpush
