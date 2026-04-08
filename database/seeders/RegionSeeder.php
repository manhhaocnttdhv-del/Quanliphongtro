<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class RegionSeeder extends Seeder
{
    /**
     * Nhập dữ liệu Tỉnh/Huyện/Xã từ API provinces.open-api.vn
     */
    public function run(): void
    {
        $this->command->info('Đang tải dữ liệu địa danh Việt Nam...');

        // ── 1. Lấy danh sách tỉnh ──────────────────────────────
        $response = Http::timeout(30)->get('https://provinces.open-api.vn/api/p/');
        if (!$response->successful()) {
            $this->command->error('Không tải được danh sách tỉnh!');
            return;
        }

        $provinces = $response->json();
        $this->command->info('Tìm thấy ' . count($provinces) . ' tỉnh/thành.');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('wards')->truncate();
        DB::table('districts')->truncate();
        DB::table('provinces')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $provinceRows = [];
        foreach ($provinces as $p) {
            $provinceRows[] = [
                'code'       => $p['code'],
                'name'       => $p['name'],
                'type'       => $p['division_type'] ?? 'tinh',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('provinces')->insert($provinceRows);
        $this->command->info('✓ Đã nhập ' . count($provinceRows) . ' tỉnh/thành.');

        // ── 2. Lấy huyện + xã theo từng tỉnh ──────────────────
        $districtRows = [];
        $wardRows     = [];
        $bar = $this->command->getOutput()->createProgressBar(count($provinces));
        $bar->start();

        foreach ($provinces as $p) {
            try {
                $detail = Http::timeout(30)
                    ->get("https://provinces.open-api.vn/api/p/{$p['code']}?depth=3")
                    ->json();

                foreach ($detail['districts'] ?? [] as $d) {
                    $districtRows[] = [
                        'code'          => $d['code'],
                        'name'          => $d['name'],
                        'type'          => $d['division_type'] ?? 'huyen',
                        'province_code' => $p['code'],
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ];

                    foreach ($d['wards'] ?? [] as $w) {
                        $wardRows[] = [
                            'code'          => $w['code'],
                            'name'          => $w['name'],
                            'type'          => $w['division_type'] ?? 'xa',
                            'district_code' => $d['code'],
                            'province_code' => $p['code'],
                            'created_at'    => now(),
                            'updated_at'    => now(),
                        ];
                    }
                }

                // Nhập từng batch để tránh memory leak
                if (count($districtRows) >= 200) {
                    DB::table('districts')->insert($districtRows);
                    $districtRows = [];
                }
                if (count($wardRows) >= 2000) {
                    DB::table('wards')->insert($wardRows);
                    $wardRows = [];
                }

            } catch (\Exception $e) {
                $this->command->warn("Lỗi tỉnh {$p['name']}: " . $e->getMessage());
            }

            $bar->advance();
            usleep(100000); // 0.1s delay để không bị rate-limit
        }

        // Nhập phần còn lại
        if (!empty($districtRows)) DB::table('districts')->insert($districtRows);
        if (!empty($wardRows))     DB::table('wards')->insert($wardRows);

        $bar->finish();
        $this->command->newLine();
        $this->command->info('✓ Hoàn thành! Đã nhập dữ liệu địa danh Việt Nam.');
        $this->command->info('  Tỉnh: '   . DB::table('provinces')->count());
        $this->command->info('  Huyện: '  . DB::table('districts')->count());
        $this->command->info('  Xã/Phường: ' . DB::table('wards')->count());
    }
}
