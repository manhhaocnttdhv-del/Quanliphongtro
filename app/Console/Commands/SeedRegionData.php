<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Province;
use App\Models\District;
use App\Models\Ward;

class SeedRegionData extends Command
{
    protected $signature   = 'region:seed {--fresh : Xoá và tạo lại toàn bộ dữ liệu}';
    protected $description = 'Seed dữ liệu Tỉnh/Huyện/Xã từ provinces.open-api.vn vào database';

    private string $baseUrl = 'https://provinces.open-api.vn/api';

    public function handle(): int
    {
        if ($this->option('fresh')) {
            $this->warn('Đang xoá dữ liệu cũ...');
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            Ward::truncate();
            District::truncate();
            Province::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        $this->info('=== Seed dữ liệu vùng lãnh thổ Việt Nam ===');

        // ─── BƯỚC 1: Seed Provinces ───────────────────
        $this->info('📍 Đang tải danh sách tỉnh/thành...');
        $provinces = $this->fetch('/p/');
        if (!$provinces) return self::FAILURE;

        $bar = $this->output->createProgressBar(count($provinces));
        $bar->start();

        foreach ($provinces as $p) {
            Province::updateOrCreate(
                ['code' => str_pad($p['code'], 2, '0', STR_PAD_LEFT)],
                ['name' => $p['name'], 'type' => $p['division_type'] ?? null]
            );
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();
        $this->info('✅ ' . count($provinces) . ' tỉnh/thành đã lưu.');

        // ─── BƯỚC 2: Seed Districts ───────────────────
        $this->info('🏙️  Đang tải quận/huyện (có thể mất vài phút)...');
        $districtCount = 0;
        $wardCount     = 0;

        $bar2 = $this->output->createProgressBar(count($provinces));
        $bar2->start();

        foreach ($provinces as $p) {
            $provCode = str_pad($p['code'], 2, '0', STR_PAD_LEFT);
            // depth=3 = province → districts → wards (1 call per province)
            $detail   = $this->fetch("/p/{$p['code']}?depth=3");
            if (!$detail || empty($detail['districts'])) { $bar2->advance(); continue; }

            foreach ($detail['districts'] as $d) {
                $distCode = str_pad($d['code'], 3, '0', STR_PAD_LEFT);
                District::updateOrCreate(
                    ['code' => $distCode],
                    [
                        'name'          => $d['name'],
                        'type'          => $d['division_type'] ?? null,
                        'province_code' => $provCode,
                    ]
                );
                $districtCount++;

                // ─── BƯỚC 3: Wards (đã có trong depth=3) ─
                if (!empty($d['wards'])) {
                    foreach ($d['wards'] as $w) {
                        Ward::updateOrCreate(
                            ['code' => str_pad($w['code'], 5, '0', STR_PAD_LEFT)],
                            [
                                'name'          => $w['name'],
                                'type'          => $w['division_type'] ?? null,
                                'district_code' => $distCode,
                                'province_code' => $provCode,
                            ]
                        );
                        $wardCount++;
                    }
                }
            }
            $bar2->advance();
        }
        $bar2->finish();
        $this->newLine();

        $this->info("✅ {$districtCount} quận/huyện và {$wardCount} phường/xã đã lưu.");
        $this->info('🎉 Hoàn thành! Dữ liệu địa phương đã sẵn sàng.');
        return self::SUCCESS;
    }

    private function fetch(string $path): ?array
    {
        try {
            $response = Http::timeout(30)->get($this->baseUrl . $path);
            if ($response->successful()) {
                return $response->json();
            }
            $this->error("Lỗi API: {$response->status()} - {$path}");
            return null;
        } catch (\Exception $e) {
            $this->error("Không kết nối được: " . $e->getMessage());
            return null;
        }
    }
}
