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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('password');
            $table->string('distributor_code')->unique();
            $table->string('distributor_name');
            $table->string('distributor_email')->unique();
            $table->string('distributor_phone')->nullable();
            $table->string('distributor_address')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedInteger('level')->default(1);
            $table->date('join_date')->nullable();
            $table->string('status')->default('active');
            $table->foreign('parent_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password');
            $table->dropForeign(['parent_id']);
            $table->dropColumn([
                'distributor_code',
                'distributor_name',
                'distributor_email',
                'distributor_phone',
                'distributor_address',
                'parent_id',
                'level',
                'join_date',
                'status',
            ]);
        });
    }
};
