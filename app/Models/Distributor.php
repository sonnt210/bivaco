<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Distributor extends Model
{
    use HasFactory;

    protected $fillable = [
        'distributor_code',
        'distributor_name',
        'distributor_email',
        'distributor_phone',
        'distributor_address',
        'parent_id',
        'level',
        'path',
        'status',
        'join_date'
    ];

    protected $casts = [
        'join_date' => 'date',
    ];

    // Quan hệ với distributor cha
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Distributor::class, 'parent_id');
    }

    // Quan hệ với các distributor con
    public function children(): HasMany
    {
        return $this->hasMany(Distributor::class, 'parent_id');
    }

    // Quan hệ với orders
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // Scope để lấy distributor theo cấp độ
    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    // Scope để lấy distributor theo status
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope để lấy distributor theo khoảng cấp độ
    public function scopeByLevelRange($query, $minLevel, $maxLevel)
    {
        return $query->whereBetween('level', [$minLevel, $maxLevel]);
    }

    // Scope để lấy distributor có cấp độ cao hơn
    public function scopeByMinLevel($query, $level)
    {
        return $query->where('level', '>=', $level);
    }

    // Scope để lấy distributor có cấp độ thấp hơn
    public function scopeByMaxLevel($query, $level)
    {
        return $query->where('level', '<=', $level);
    }

    // Method để lấy tất cả distributor theo cấp độ cụ thể
    public static function getDistributorsByLevel($level)
    {
        return self::where('level', $level)->active()->get();
    }

    // Method để lấy tất cả cấp độ có trong hệ thống
    public static function getAllLevels()
    {
        return self::distinct()->pluck('level')->sort()->values();
    }

    // Method để lấy thống kê theo cấp độ
    public static function getLevelStatistics()
    {
        $levels = self::getAllLevels();
        $statistics = [];
        
        foreach ($levels as $level) {
            $count = self::where('level', $level)->active()->count();
            $sales = Order::byDistributorLevel($level)->sum('amount');
            
            $statistics["F{$level}"] = [
                'level' => $level,
                'count' => $count,
                'total_sales' => $sales,
                'description' => self::getLevelDescription($level)
            ];
        }
        
        return $statistics;
    }

    // Method để lấy mô tả cấp độ
    public static function getLevelDescription($level)
    {
        $descriptions = [
            1 => 'Distributor max level',
            2 => 'Distributor level 2',
            3 => 'Distributor level 3',
            4 => 'Distributor level 4',
            5 => 'Distributor level 5',
            6 => 'Distributor level 6',
            7 => 'Distributor level 7',
            8 => 'Distributor level 8',
            9 => 'Distributor level 9',
            10 => 'Distributor level 10'
        ];
        
        return $descriptions[$level] ?? "Distributor level: {$level}";
    }

    // Method để lấy cấp độ của distributor hiện tại
    public function getLevelName(): string
    {
        return $this->level;
    }

    // Method để lấy tất cả con cháu (recursive)
    public function getAllDescendants()
    {
        return $this->children()->with('children')->get();
    }

    // Method để lấy tất cả tổ tiên (ancestors)
    public function getAllAncestors()
    {
        $ancestors = collect();
        $current = $this->parent;
        
        while ($current) {
            $ancestors->push($current);
            $current = $current->parent;
        }
        
        return $ancestors->reverse();
    }

    // Method để lấy đường dẫn phân cấp
    public function getHierarchyPath()
    {
        $ancestors = $this->getAllAncestors();
        $path = $ancestors->pluck('distributor_name')->push($this->distributor_name);
        
        return $path->implode(' > ');
    }

    // Method để lấy tổng doanh số theo cấp độ
    public function getTotalSalesByLevel($level = null)
    {
        $query = $this->orders();
        
        if ($level) {
            $query->where('distributor_level', $level);
        }
        
        return $query->sum('amount');
    }

    // Method để lấy tổng doanh số của toàn bộ hệ thống con
    public function getTotalSubNetworkSales()
    {
        $descendantIds = $this->getAllDescendantIds();
        $descendantIds->push($this->id);
        
        return Order::whereIn('distributor_id', $descendantIds)->sum('amount');
    }

    // Method để lấy tất cả ID của con cháu
    public function getAllDescendantIds()
    {
        $ids = collect();
        $this->collectDescendantIds($ids);
        return $ids;
    }

    // Helper method để thu thập ID con cháu
    private function collectDescendantIds(&$ids)
    {
        foreach ($this->children as $child) {
            $ids->push($child->id);
            $child->collectDescendantIds($ids);
        }
    }

    // Method để kiểm tra xem có thể thêm con không
    public function canAddChild()
    {
        // Có thể giới hạn số cấp độ tối đa
        $maxLevel = config('distributor.max_level', 10);
        return $this->level < $maxLevel;
    }

    // Method để lấy số lượng con trực tiếp
    public function getDirectChildrenCount()
    {
        return $this->children()->count();
    }

    // Method để lấy tổng số con cháu
    public function getTotalDescendantsCount()
    {
        return $this->getAllDescendantIds()->count();
    }

    // Method để lấy thông tin tổng quan về distributor network
    public function getDistributorNetworkOverview()
    {
        return [
            'level' => $this->level,
            'level_name' => $this->getLevelName(),
            'direct_children' => $this->getDirectChildrenCount(),
            'total_descendants' => $this->getTotalDescendantsCount(),
            'total_sales' => $this->getTotalSubNetworkSales(),
            'hierarchy_path' => $this->getHierarchyPath(),
            'can_add_child' => $this->canAddChild()
        ];
    }
} 