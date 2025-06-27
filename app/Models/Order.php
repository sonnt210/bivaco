<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'distributor_id',
        'distributor_level',
        'amount',
        'sale_time',
        'bill_code',
        'notes'
    ];

    protected $casts = [
        'sale_time' => 'datetime',
        'amount' => 'decimal:2',
    ];

    // Quan hệ với distributor
    public function distributor(): BelongsTo
    {
        return $this->belongsTo(Distributor::class);
    }

    // Scope để lấy orders theo cấp độ distributor
    public function scopeByDistributorLevel($query, $level)
    {
        return $query->where('distributor_level', $level);
    }

    // Scope để lấy orders theo khoảng thời gian
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('sale_time', [$startDate, $endDate]);
    }
} 