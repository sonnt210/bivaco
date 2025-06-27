<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Distributor Configuration
    |--------------------------------------------------------------------------
    |
    | Cấu hình cho hệ thống quản lý distributor
    |
    */

    // Số cấp độ tối đa trong hệ thống
    'max_level' => env('DISTRIBUTOR_MAX_LEVEL', 10),

    // Cấu hình cho từng cấp độ
    'levels' => [
        1 => [
            'name' => 'F1',
            'description' => 'Distributor cấp cao nhất (Gốc)',
            'commission_rate' => 0.10, // 10%
            'min_orders' => 0,
            'can_add_children' => true,
        ],
        2 => [
            'name' => 'F2',
            'description' => 'Distributor cấp 2',
            'commission_rate' => 0.08, // 8%
            'min_orders' => 5,
            'can_add_children' => true,
        ],
        3 => [
            'name' => 'F3',
            'description' => 'Distributor cấp 3',
            'commission_rate' => 0.06, // 6%
            'min_orders' => 10,
            'can_add_children' => true,
        ],
        4 => [
            'name' => 'F4',
            'description' => 'Distributor cấp 4',
            'commission_rate' => 0.05, // 5%
            'min_orders' => 15,
            'can_add_children' => true,
        ],
        5 => [
            'name' => 'F5',
            'description' => 'Distributor cấp 5',
            'commission_rate' => 0.04, // 4%
            'min_orders' => 20,
            'can_add_children' => true,
        ],
        6 => [
            'name' => 'F6',
            'description' => 'Distributor cấp 6',
            'commission_rate' => 0.03, // 3%
            'min_orders' => 25,
            'can_add_children' => true,
        ],
        7 => [
            'name' => 'F7',
            'description' => 'Distributor cấp 7',
            'commission_rate' => 0.02, // 2%
            'min_orders' => 30,
            'can_add_children' => true,
        ],
        8 => [
            'name' => 'F8',
            'description' => 'Distributor cấp 8',
            'commission_rate' => 0.015, // 1.5%
            'min_orders' => 35,
            'can_add_children' => true,
        ],
        9 => [
            'name' => 'F9',
            'description' => 'Distributor cấp 9',
            'commission_rate' => 0.01, // 1%
            'min_orders' => 40,
            'can_add_children' => true,
        ],
        10 => [
            'name' => 'F10',
            'description' => 'Distributor cấp 10',
            'commission_rate' => 0.005, // 0.5%
            'min_orders' => 50,
            'can_add_children' => false, // Cấp cuối cùng
        ],
    ],

    // Cấu hình phân trang
    'pagination' => [
        'per_page' => 20,
        'max_per_page' => 100,
    ],

    // Cấu hình tìm kiếm
    'search' => [
        'min_query_length' => 2,
        'max_results' => 50,
    ],

    // Cấu hình thống kê
    'statistics' => [
        'cache_duration' => 3600, // 1 giờ
        'refresh_interval' => 300, // 5 phút
    ],

    // Cấu hình validation
    'validation' => [
        'distributor_code' => [
            'min_length' => 3,
            'max_length' => 20,
            'pattern' => '/^[A-Z0-9]+$/',
        ],
        'distributor_name' => [
            'min_length' => 2,
            'max_length' => 255,
        ],
        'distributor_phone' => [
            'min_length' => 10,
            'max_length' => 20,
        ],
    ],

    // Cấu hình export
    'export' => [
        'formats' => ['csv', 'xlsx', 'pdf'],
        'max_records' => 10000,
        'chunk_size' => 1000,
    ],

    // Cấu hình notification
    'notifications' => [
        'new_distributor' => true,
        'level_upgrade' => true,
        'commission_earned' => true,
        'order_placed' => true,
    ],

    // Cấu hình commission
    'commission' => [
        'calculation_method' => 'percentage', // percentage, fixed, tiered
        'minimum_payout' => 100000, // VND
        'payout_schedule' => 'monthly', // daily, weekly, monthly
        'include_own_sales' => true,
        'include_downline_sales' => true,
    ],

    // Cấu hình performance tracking
    'performance' => [
        'track_sales' => true,
        'track_orders' => true,
        'track_customers' => true,
        'track_commission' => true,
        'retention_period' => 365, // days
    ],
]; 