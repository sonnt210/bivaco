<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bonus_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('distributor_id');
            $table->string('month_year'); // Format: YYYY-MM
            $table->decimal('personal_sales', 15, 2)->default(0); // Doanh số cá nhân tháng hiện tại
            $table->decimal('personal_sales_t1', 15, 2)->default(0); // Doanh số cá nhân tháng T-1
            $table->decimal('personal_sales_t2', 15, 2)->default(0); // Doanh số cá nhân tháng T-2
            $table->decimal('branch_sales', 15, 2)->default(0); // Doanh số nhánh tháng hiện tại
            $table->decimal('branch_sales_t1', 15, 2)->default(0); // Doanh số nhánh tháng T-1
            $table->decimal('branch_sales_t2', 15, 2)->default(0); // Doanh số nhánh tháng T-2
            $table->integer('qualified_branches')->default(0); // Số nhánh đạt chuẩn tháng hiện tại
            $table->integer('qualified_branches_t1')->default(0); // Số nhánh đạt chuẩn tháng T-1
            $table->integer('qualified_branches_t2')->default(0); // Số nhánh đạt chuẩn tháng T-2
            $table->boolean('personal_condition_met')->default(false); // Điều kiện doanh số cá nhân
            $table->boolean('branch_condition_met')->default(false); // Điều kiện doanh số nhánh
            $table->boolean('is_qualified')->default(false); // Đủ điều kiện nhận thưởng
            $table->decimal('bonus_amount', 15, 2)->default(0); // Số tiền thưởng nhận được
            $table->integer('consecutive_failures')->default(0); // Số tháng liên tiếp không đạt 5 triệu
            $table->boolean('title_lost')->default(false); // Mất danh hiệu (5 tháng liên tiếp)
            $table->text('notes')->nullable(); // Ghi chú
            $table->timestamps();

            $table->foreign('distributor_id')->references('id')->on('distributors')->onDelete('cascade');
            $table->unique(['distributor_id', 'month_year']);
            $table->index(['month_year', 'is_qualified']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bonus_records');
    }
};
