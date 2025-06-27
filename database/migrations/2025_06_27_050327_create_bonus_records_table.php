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
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('month_year', 7);
            $table->decimal('personal_sales', 15, 2)->default(0);
            $table->decimal('personal_sales_t1', 15, 2)->default(0);
            $table->decimal('personal_sales_t2', 15, 2)->default(0);
            $table->decimal('branch_sales', 15, 2)->default(0);
            $table->decimal('branch_sales_t1', 15, 2)->default(0);
            $table->decimal('branch_sales_t2', 15, 2)->default(0);
            $table->unsignedInteger('qualified_branches')->default(0);
            $table->unsignedInteger('qualified_branches_t1')->default(0);
            $table->unsignedInteger('qualified_branches_t2')->default(0);
            $table->boolean('personal_condition_met')->default(false);
            $table->boolean('branch_condition_met')->default(false);
            $table->boolean('is_qualified')->default(false);
            $table->decimal('bonus_amount', 15, 2)->default(0);
            $table->unsignedInteger('consecutive_failures')->default(0);
            $table->boolean('title_lost')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'month_year']);
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
