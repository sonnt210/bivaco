<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Seed 5 nhà phân phối F1
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        \App\Models\BonusRecord::truncate();
        \App\Models\Order::truncate();
        \App\Models\User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $faker = \Faker\Factory::create();
        $f1s = [];
        foreach (range(1, 5) as $i) {
            $f1 = \App\Models\User::create([
                'name' => 'F1 Nhà phân phối ' . $i,
                'email' => 'f1npp' . $i . '@mail.com',
                'distributor_code' => 'F1NPP' . $i,
                'distributor_name' => 'F1 Nhà phân phối ' . $i,
                'distributor_email' => 'f1npp' . $i . '@mail.com',
                'distributor_phone' => $faker->phoneNumber(),
                'distributor_address' => $faker->address(),
                'parent_id' => null,
                'level' => 1,
                'join_date' => $faker->dateTimeBetween('-2 years', 'now'),
                'status' => 'active',
            ]);
            $f1s[] = $f1;

            // Seed 3 F2 cho mỗi F1
            $f2s = [];
            foreach (range(1, 3) as $j) {
                $f2 = \App\Models\User::create([
                    'name' => 'F2 Nhà phân phối ' . $i . '-' . $j,
                    'email' => 'f2npp' . $i . $j . '@mail.com',
                    'distributor_code' => 'F2NPP' . $i . $j,
                    'distributor_name' => 'F2 Nhà phân phối ' . $i . '-' . $j,
                    'distributor_email' => 'f2npp' . $i . $j . '@mail.com',
                    'distributor_phone' => $faker->phoneNumber(),
                    'distributor_address' => $faker->address(),
                    'parent_id' => $f1->id,
                    'level' => 2,
                    'join_date' => $faker->dateTimeBetween('-2 years', 'now'),
                    'status' => 'active',
                ]);
                $f2s[] = $f2;
            }

            // Đảm bảo F1 có doanh số cá nhân >= 5 triệu/tháng trong 3 tháng liên tiếp
            foreach (['2025-04', '2025-05', '2025-06'] as $month) {
                $date = \Carbon\Carbon::createFromFormat('Y-m', $month)->startOfMonth();
                \App\Models\Order::create([
                    'user_id' => $f1->id,
                    'user_level' => 1,
                    'amount' => 6000000 + rand(0, 2000000),
                    'sale_time' => $date->copy()->addDays(rand(0, 27)),
                    'bill_code' => 'F1' . $i . $month . strtoupper(\Illuminate\Support\Str::random(4)),
                ]);
            }

            // Đảm bảo ít nhất 2 F2 đạt >= 250 triệu/tháng trong 3 tháng liên tiếp
            foreach (array_slice($f2s, 0, 2) as $f2) {
                foreach (['2025-04', '2025-05', '2025-06'] as $month) {
                    $date = \Carbon\Carbon::createFromFormat('Y-m', $month)->startOfMonth();
                    \App\Models\Order::create([
                        'user_id' => $f2->id,
                        'user_level' => 2,
                        'amount' => 260000000 + rand(0, 10000000),
                        'sale_time' => $date->copy()->addDays(rand(0, 27)),
                        'bill_code' => 'F2' . $f2->id . $month . strtoupper(\Illuminate\Support\Str::random(4)),
                    ]);
                }
            }
            // F2 còn lại chỉ có doanh số nhỏ hơn 250 triệu
            $f2 = $f2s[2];
            foreach (['2025-04', '2025-05', '2025-06'] as $month) {
                $date = \Carbon\Carbon::createFromFormat('Y-m', $month)->startOfMonth();
                \App\Models\Order::create([
                    'user_id' => $f2->id,
                    'user_level' => 2,
                    'amount' => 10000000 + rand(0, 20000000),
                    'sale_time' => $date->copy()->addDays(rand(0, 27)),
                    'bill_code' => 'F2' . $f2->id . $month . strtoupper(\Illuminate\Support\Str::random(4)),
                ]);
            }
        }
    }
}
