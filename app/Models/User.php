<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Quan hệ với user cha
    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    // Quan hệ với các user con
    public function children(): HasMany
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    // Quan hệ với orders
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // Scope để lấy user theo cấp độ
    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    // Scope để lấy user theo status
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope để lấy user theo khoảng cấp độ
    public function scopeByLevelRange($query, $minLevel, $maxLevel)
    {
        return $query->whereBetween('level', [$minLevel, $maxLevel]);
    }

    // Scope để lấy user có cấp độ cao hơn
    public function scopeByMinLevel($query, $level)
    {
        return $query->where('level', '>=', $level);
    }

    // Scope để lấy user có cấp độ thấp hơn
    public function scopeByMaxLevel($query, $level)
    {
        return $query->where('level', '<=', $level);
    }

    // Method để lấy tất cả user theo cấp độ cụ thể
    public static function getUsersByLevel($level)
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
            $sales = Order::byUserLevel($level)->sum('amount');
            
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
            1 => 'User max level',
            2 => 'User level 2',
            3 => 'User level 3',
            4 => 'User level 4',
            5 => 'User level 5',
            6 => 'User level 6',
            7 => 'User level 7',
            8 => 'User level 8',
            9 => 'User level 9',
            10 => 'User level 10'
        ];
        
        return $descriptions[$level] ?? "User level: {$level}";
    }

    // Method để lấy cấp độ của user hiện tại
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
        $path = $ancestors->pluck('name')->push($this->name);
        
        return $path->implode(' > ');
    }

    // Method để lấy tổng doanh số theo cấp độ
    public function getTotalSalesByLevel($level = null)
    {
        $query = $this->orders();
        
        if ($level) {
            $query->where('user_level', $level);
        }
        
        return $query->sum('amount');
    }

    // Method để lấy tổng doanh số của toàn bộ hệ thống con
    public function getTotalSubNetworkSales()
    {
        $descendantIds = $this->getAllDescendantIds();
        $descendantIds->push($this->id);
        
        return Order::whereIn('user_id', $descendantIds)->sum('amount');
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

    // Method để kiểm tra có thể thêm con không
    public function canAddChild()
    {
        $maxLevel = config('user.max_level', 10);
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

    // Method để lấy tổng quan network
    public function getUserNetworkOverview()
    {
        $descendantIds = $this->getAllDescendantIds();
        $descendantIds->push($this->id);

        $totalSales = Order::whereIn('user_id', $descendantIds)->sum('amount');
        $totalOrders = Order::whereIn('user_id', $descendantIds)->count();
        $totalUsers = $descendantIds->count();

        return [
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'total_users' => $totalUsers,
            'average_order_value' => $totalOrders > 0 ? $totalSales / $totalOrders : 0
        ];
    }
}
