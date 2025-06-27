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
        Schema::create('distributors', function (Blueprint $table) {
            $table->id();
            $table->string('distributor_code')->unique();
            $table->string('distributor_name');
            $table->string('distributor_email')->unique();
            $table->string('distributor_phone');
            $table->string('distributor_address');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedInteger('level')->default(1);
            $table->string('path')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->date('join_date');
            $table->timestamps();
            
            $table->foreign('parent_id')->references('id')->on('distributors')->onDelete('cascade');
            $table->index(['level', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributors');
    }
};
