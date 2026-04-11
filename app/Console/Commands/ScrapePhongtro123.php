<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Room;
use App\Models\RoomImage;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ScrapePhongtro123 extends Command
{
    protected $signature = 'scrape:phongtro123 {--pages=2 : Số trang mỗi thành phố} {--clean : Xóa hết phòng cũ trước} {--only= : Chỉ cào city theo tên (vd: hcm, hanoi, nghe-an)}';
    protected $description = 'Cào dữ liệu phòng trọ từ phongtro123.com (Nghệ An, Hà Nội, HCM)';

    // Danh sách thành phố + chủ trọ mẫu
    private array $cities = [
        [
            'name' => 'Nghệ An - TP Vinh',
            'url' => 'https://phongtro123.com/tinh-thanh/nghe-an/thanh-pho-vinh',
            'province' => 'Tỉnh Nghệ An',
            'district' => 'Thành phố Vinh',
            'lat' => 18.6796,
            'lng' => 105.6813,
            'landlords' => [
                ['name' => 'Trần Văn Minh', 'email' => 'minh.tran@gmail.com', 'phone' => '0912345678'],
                ['name' => 'Nguyễn Thị Lan', 'email' => 'lan.nguyen@gmail.com', 'phone' => '0923456789'],
            ],
        ],
        [
            'name' => 'Hà Nội',
            'url' => 'https://phongtro123.com/tinh-thanh/ha-noi',
            'province' => 'Thành phố Hà Nội',
            'district' => '',
            'lat' => 21.0285,
            'lng' => 105.8542,
            'landlords' => [
                ['name' => 'Phạm Đức Anh', 'email' => 'anh.pham.hn@gmail.com', 'phone' => '0934567890'],
                ['name' => 'Vũ Thị Hồng', 'email' => 'hong.vu.hn@gmail.com', 'phone' => '0945678901'],
            ],
        ],
        [
            'name' => 'TP Hồ Chí Minh',
            'url' => 'https://phongtro123.com/tinh-thanh/ho-chi-minh',
            'province' => 'Thành phố Hồ Chí Minh',
            'district' => '',
            'lat' => 10.8231,
            'lng' => 106.6297,
            'landlords' => [
                ['name' => 'Lê Hoàng Phúc', 'email' => 'phuc.le.hcm@gmail.com', 'phone' => '0956789012'],
                ['name' => 'Đỗ Thị Mai', 'email' => 'mai.do.hcm@gmail.com', 'phone' => '0967890123'],
            ],
        ],
    ];

    public function handle()
    {
        $pages = (int) $this->option('pages');

        if ($this->option('clean')) {
            $this->info('🗑️  Xóa toàn bộ phòng cũ...');
            RoomImage::query()->delete();
            Room::query()->delete();
            $this->info('   Đã xóa hết.');
        }

        $totalImported = 0;

        $only = strtolower($this->option('only') ?? '');
        $citiesToRun = $this->cities;
        if ($only) {
            $citiesToRun = array_filter($this->cities, function ($c) use ($only) {
                return str_contains(strtolower($c['name']), $only)
                    || str_contains(strtolower($c['url']), $only);
            });
        }

        foreach ($citiesToRun as $city) {
            $this->info("\n" . str_repeat('═', 60));
            $this->info("📍 {$city['name']}");
            $this->info(str_repeat('═', 60));

            // Tạo/lấy chủ trọ cho thành phố này
            $landlords = $this->ensureLandlords($city);
            $landlordIdx = 0;

            for ($page = 1; $page <= $pages; $page++) {
                $url = $city['url'];
                if ($page > 1)
                    $url .= '?page=' . $page;

                $this->info("\n  📄 Trang {$page}...");

                $html = $this->fetchUrl($url);
                if (!$html)
                    continue;

                $listings = $this->parseListingPage($html);
                if (empty($listings)) {
                    $this->warn("     Không tìm thấy phòng nào, dừng.");
                    break;
                }

                $this->info("     Tìm thấy " . count($listings) . " phòng");

                foreach ($listings as $listing) {
                    $detail = $this->parseDetailPage($listing['url'], $city);

                    $currentLandlord = $landlords[$landlordIdx % count($landlords)];
                    $landlordIdx++;

                    $room = $this->importRoom($listing, $detail, $currentLandlord, $city);

                    if ($room) {
                        // Download images

                        $imgCount = $this->downloadImages($room, $detail['images'] ?? []);
                        if ($imgCount == 0) {
                            $this->line("Chuyển qua dòng khác");
                            continue;
                        }
                        $totalImported++;
                        $priceStr = number_format((float) $room->price);
                        $this->line("     ✅ {$room->name}");
                        $this->line("        💰 {$priceStr}đ | 📐 {$room->area}m² | 🖼️ {$imgCount} ảnh");
                    }

                    usleep(800000); // 0.8s delay
                }
            }
        }

        $this->info("\n" . str_repeat('═', 60));
        $this->info("🎉 Hoàn tất! Đã import {$totalImported} phòng trọ từ 3 thành phố.");
        $this->info(str_repeat('═', 60));
        return 0;
    }

    private function ensureLandlords(array $city): array
    {
        $landlords = [];
        foreach ($city['landlords'] as $d) {
            $landlords[] = User::updateOrCreate(
                ['email' => $d['email']],
                [
                    'name' => $d['name'],
                    'phone' => $d['phone'],
                    'password' => \Illuminate\Support\Facades\Hash::make('password'),
                    'role' => 'landlord',
                    'province_name' => $city['province'],
                    'district_name' => $city['district'] ?: null,
                ]
            );
        }
        return $landlords;
    }

    private function fetchUrl(string $url): ?string
    {
        try {
            return Http::timeout(30)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'])
                ->get($url)
                ->body();
        } catch (\Exception $e) {
            $this->error("     Lỗi: " . $e->getMessage());
            return null;
        }
    }

    private function parseListingPage(string $html): array
    {
        $listings = [];

        // Step 1: Extract all unique -prNNNNNN.html URLs
        preg_match_all('/href="(\/[^"]*-pr\d+\.html)"/iu', $html, $urlMatches);
        $allUrls = array_unique($urlMatches[1] ?? []);

        foreach ($allUrls as $url) {
            // Step 2: find name from a 800-char window around that URL
            $pos = strpos($html, $url);
            if ($pos === false)
                continue;

            $window = substr($html, $pos, 800);

            // Extract any text inside tags (>text<) that is 10+ chars
            $name = '';
            if (preg_match_all('/>([^<]{10,})</u', $window, $txt)) {
                foreach ($txt[1] as $candidate) {
                    $candidate = trim(html_entity_decode($candidate));
                    if (mb_strlen($candidate) >= 10 && !preg_match('/^\d+$/', $candidate)) {
                        $name = $candidate;
                        break;
                    }
                }
            }

            if (mb_strlen($name) < 5)
                continue;

            // Find price near this URL
            $priceText = '';
            if (preg_match('/([\d.,]+)\s*(?:triệu|tr)/ui', $window, $pm)) {
                $priceText = $pm[0];
            }

            $listings[] = [
                'url' => $url,
                'name' => $name,
                'price_text' => $priceText,
                'area_text' => '',
            ];
        }

        return $listings;
    }


    private function parseDetailPage(string $url, array $city = []): array
    {
        if (!str_starts_with($url, 'http')) {
            $url = 'https://phongtro123.com' . $url;
        }

        $html = $this->fetchUrl($url);
        if (!$html)
            return [];

        $detail = [];

        // Description
        if (preg_match('/class="[^"]*(?:post-content|section-content|detail-content)[^"]*"[^>]*>(.*?)<\/(?:div|section)>/si', $html, $m)) {
            $detail['description'] = trim(strip_tags($m[1]));
        } elseif (preg_match('/<h2[^>]*>\s*Thông tin mô tả\s*<\/h2>(.*?)(?:<h2|<\/section)/si', $html, $m)) {
            $detail['description'] = trim(strip_tags($m[1]));
        }

        // Price
        if (preg_match('/([\d.,]+)\s*(?:triệu|tr)\/tháng/ui', $html, $m)) {
            $detail['price_text'] = $m[0];
        } elseif (preg_match('/([\d.,]+)\s*(?:triệu|tr)/ui', $html, $m)) {
            $detail['price_text'] = $m[0];
        }

        // Area
        if (preg_match('/([\d.,]+)\s*m²/u', $html, $m)) {
            $detail['area_text'] = $m[0];
        }

        // Address — ưu tiên lấy từ thẻ chứa tên tỉnh/thành phố đang cào
        // để tránh bắt nhầm địa chỉ từ nội dung mô tả bài đăng
        $detail['address'] = '';
        if (preg_match('/Địa chỉ[:\s]*([^<\n]{10,200})/ui', $html, $m)) {
            $rawAddress = trim(strip_tags(html_entity_decode($m[1])));

            // Validate: địa chỉ phải thuộc đúng tỉnh/thành phố đang cào
            // Nếu có city context thì kiểm tra, không thì dùng luôn
            if (!empty($city['province'])) {
                $provinceKeyword = $this->extractProvinceKeyword($city['province']);
                if (mb_stripos($rawAddress, $provinceKeyword) !== false) {
                    $detail['address'] = $rawAddress;
                }
                // Nếu không khớp tỉnh → bỏ, để importRoom() fallback về city config
            } else {
                $detail['address'] = $rawAddress;
            }
        }

        // District — chỉ parse nếu địa chỉ đã được xác nhận thuộc đúng tỉnh
        $detail['district'] = '';
        if (!empty($detail['address'])) {
            if (preg_match('/(?:Quận|Huyện|Thành phố|Thị xã)\s+([^,]+)/u', $detail['address'], $dm)) {
                $detail['district'] = trim($dm[0]);
            }
        }

        // Ward — chỉ parse nếu địa chỉ đã được xác nhận thuộc đúng tỉnh
        $detail['ward'] = '';
        if (!empty($detail['address'])) {
            if (preg_match('/(?:Phường|Xã|Thị trấn)\s+([^,]+)/u', $detail['address'], $wm)) {
                $detail['ward'] = trim($wm[0]);
            }
        }

        // Phone
        if (preg_match('/tel:(\d{9,11})/', $html, $m)) {
            $detail['phone'] = $m[1];
        }

        // Images - from CDN (900x600 thumbs)
        $detail['images'] = [];
        preg_match_all('/(?:src|data-src)="(https:\/\/pt123\.cdn\.static123\.com\/images\/thumbs\/900x600\/[^"]+)"/i', $html, $imgMatches);
        if (!empty($imgMatches[1])) {
            $detail['images'] = array_values(array_unique(array_slice($imgMatches[1], 0, 5)));
        }

        return $detail;
    }

    /**
     * Lấy từ khóa ngắn để match tỉnh/thành phố:
     * "Tỉnh Nghệ An" → "Nghệ An", "Thành phố Hồ Chí Minh" → "Hồ Chí Minh", ...
     */
    private function extractProvinceKeyword(string $province): string
    {
        $province = preg_replace('/^(?:Tỉnh|Thành phố)\s+/ui', '', $province);
        return trim($province);
    }

    private function downloadImages(Room $room, array $imageUrls): int
    {
        $count = 0;
        foreach ($imageUrls as $imgUrl) {
            try {
                $response = Http::timeout(10)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0', 'Referer' => 'https://phongtro123.com/'])
                    ->get($imgUrl);

                if (!$response->successful())
                    continue;

                $imageData = $response->body();

                // Crop bottom 15% to remove phongtro123.com watermark
                $imageData = $this->cropWatermark($imageData);

                $ext = pathinfo(parse_url($imgUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                $filename = 'rooms/' . $room->id . '_' . Str::random(8) . '.' . $ext;

                Storage::disk('public')->put($filename, $imageData);

                RoomImage::create([
                    'room_id' => $room->id,
                    'image_path' => $filename,
                ]);

                $count++;
            } catch (\Exception $e) {
                // Skip failed images
            }
        }
        return $count;
    }

    /**
     * Remove phongtro123.com watermark using GD inpainting.
     * Detects the bright watermark band at the bottom,
     * then clones pixels from above to cover it.
     */
    private function cropWatermark(string $imageData): string
    {
        $src = @imagecreatefromstring($imageData);
        if (!$src)
            return $imageData;

        $w = imagesx($src);
        $h = imagesy($src);

        // Watermark is in bottom ~50px band
        $bandH = min(60, (int) ($h * 0.10));
        $bandStart = $h - $bandH;

        // Scan each pixel in the watermark band
        // If pixel is very bright (watermark text/bar), replace with pixel from above
        for ($y = $bandStart; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $rgb = imagecolorat($src, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                $brightness = ($r + $g + $b) / 3;

                // If pixel is bright (watermark overlay), clone from above
                if ($brightness > 180) {
                    // Sample from mirrored position above the band
                    $srcY = max(0, $bandStart - ($y - $bandStart) - 1);
                    $srcColor = imagecolorat($src, $x, $srcY);
                    imagesetpixel($src, $x, $y, $srcColor);
                }
            }
        }

        ob_start();
        imagejpeg($src, null, 92);
        $result = ob_get_clean();
        imagedestroy($src);

        return $result;
    }

    private function importRoom(array $listing, array $detail, User $landlord, array $city): ?Room
    {
        $name = $listing['name'];
        if (mb_strlen($name) > 80) {
            $name = mb_substr($name, 0, 77) . '...';
        }

        if (Room::where('name', $name)->exists())
            return null;

        // Price
        $price = $this->parsePrice($listing['price_text']);
        if ($price <= 0 && !empty($detail['price_text'])) {
            $price = $this->parsePrice($detail['price_text']);
        }
        if ($price <= 0)
            $price = 1500000;

        // Area
        $area = $this->parseArea($listing['area_text']);
        if (!$area && !empty($detail['area_text'])) {
            $area = $this->parseArea($detail['area_text']);
        }

        // Description
        $description = $detail['description'] ?? '';
        if (mb_strlen($description) > 1000) {
            $description = mb_substr($description, 0, 997) . '...';
        }

        // Location
        $district = ($detail['district'] ?? '') ?: $city['district'];
        $ward = ($detail['ward'] ?? '') ?: null;
        $address = $detail['address'] ?? null;

        return Room::create([
            'landlord_id' => $landlord->id,
            'name' => $name,
            'price' => $price,
            'area' => $area ?? rand(15, 30),
            'floor' => rand(1, 4),
            'description' => $description,
            'amenities' => $this->extractAmenities($description),
            'electricity_price' => [3500, 3800, 4000][rand(0, 2)],
            'water_price' => [15000, 18000, 20000][rand(0, 2)],
            'service_fee' => [0, 30000, 50000][rand(0, 2)],
            'status' => 'available',
            'approval_status' => 'approved',
            'province_name' => $city['province'],
            'district_name' => $district ?: null,
            'ward_name' => $ward,
            'address_detail' => $address,
            'latitude' => $city['lat'] + (rand(-100, 100) / 10000),
            'longitude' => $city['lng'] + (rand(-100, 100) / 10000),
        ]);
    }

    private function parsePrice(string $text): int
    {
        $text = mb_strtolower(trim($text));
        if (preg_match('/([\d.,]+)\s*(?:triệu|tr)/u', $text, $m)) {
            return (int) (floatval(str_replace(',', '.', $m[1])) * 1000000);
        }
        $num = preg_replace('/[^\d]/', '', $text);
        return $num ? (int) $num : 0;
    }

    private function parseArea(string $text): ?float
    {
        if (preg_match('/([\d.,]+)\s*m/u', $text, $m)) {
            return floatval(str_replace(',', '.', $m[1]));
        }
        return null;
    }

    private function extractAmenities(string $text): array
    {
        $amenities = [];
        $map = [
            'wifi' => 'Wifi',
            'wc riêng' => 'WC riêng',
            'khép kín' => 'Khép kín',
            'nước nóng' => 'Nước nóng',
            'nóng lạnh' => 'Nước nóng',
            'máy lạnh' => 'Máy lạnh',
            'điều hòa' => 'Điều hòa',
            'điều hoà' => 'Điều hòa',
            'tủ lạnh' => 'Tủ lạnh',
            'máy giặt' => 'Máy giặt',
            'gác lửng' => 'Gác lửng',
            'gác xép' => 'Gác xép',
            'ban công' => 'Ban công',
            'bếp' => 'Bếp nấu',
            'camera' => 'Camera',
            'để xe' => 'Chỗ để xe',
            'giữ xe' => 'Chỗ để xe',
            'tự do' => 'Tự do giờ giấc',
            'nội thất' => 'Nội thất',
            'chung chủ' => 'Không chung chủ',
        ];

        $lower = mb_strtolower($text);
        foreach ($map as $kw => $label) {
            if (mb_strpos($lower, $kw) !== false && !in_array($label, $amenities)) {
                $amenities[] = $label;
            }
        }
        return $amenities ?: ['Wifi', 'Chỗ để xe'];
    }
}
