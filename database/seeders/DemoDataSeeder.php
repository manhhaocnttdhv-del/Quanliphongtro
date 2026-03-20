<?php

namespace Database\Seeders;

use App\Models\AdminCommission;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\MaintenanceRequest;
use App\Models\RentRequest;
use App\Models\Room;
use App\Models\Setting;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('⚙️  Seeding Settings...');
        $this->seedSettings();

        $this->command->info('🏠 Seeding Landlords & Tenants...');
        [$landlords, $tenants] = $this->seedUsers();

        $this->command->info('🏢 Seeding Rooms...');
        $rooms = $this->seedRooms($landlords);

        $this->command->info('📋 Seeding Rent Requests...');
        $this->seedRentRequests($rooms, $tenants);

        $this->command->info('📄 Seeding Contracts + Utilities + Invoices...');
        $contracts = $this->seedContracts($rooms, $tenants);

        $this->command->info('🔧 Seeding Maintenance Requests...');
        $this->seedMaintenance($rooms, $tenants);

        $this->command->info('💰 Seeding Admin Commissions...');
        $this->seedCommissions($contracts);

        $this->command->info('✅ Tất cả module đã được seed!');
    }

    // ─────────────────────────────────────────────────────────
    // 1. Cài đặt hệ thống
    // ─────────────────────────────────────────────────────────
    private function seedSettings(): void
    {
        $defaults = [
            'site_name'                 => 'Nhà Trọ Vinh City',
            'site_phone'                => '0912 345 678',
            'site_email'                => 'contact@nhatro.vn',
            'site_address'              => '25 Đường Lê Lợi, Phường Quang Trung, TP. Vinh, Nghệ An',
            'site_description'          => 'Hệ thống quản lý phòng trọ hiện đại - Kết nối chủ trọ và người thuê nhanh chóng, tiện lợi.',
            'default_province'          => 'Tỉnh Nghệ An',
            'default_electricity_price' => '3500',
            'default_water_price'       => '15000',
            'vietqr_bank_id'            => 'MB',
            'vietqr_account_no'         => '0123456789',
            'momo_number'               => '0912345678',
        ];
        foreach ($defaults as $key => $value) {
            Setting::set($key, $value);
        }
    }

    // ─────────────────────────────────────────────────────────
    // 2. Users
    // ─────────────────────────────────────────────────────────
    private function seedUsers(): array
    {
        $landlordData = [
            ['name' => 'Trần Văn Minh',   'email' => 'minh.tran@gmail.com',   'phone' => '0912345678', 'province' => 'Tỉnh Nghệ An', 'district' => 'Thành phố Vinh'],
            ['name' => 'Nguyễn Thị Lan',  'email' => 'lan.nguyen@gmail.com',  'phone' => '0923456789', 'province' => 'Tỉnh Nghệ An', 'district' => 'Thành phố Vinh'],
            ['name' => 'Lê Hoàng Phúc',   'email' => 'phuc.le@gmail.com',     'phone' => '0934567890', 'province' => 'Tỉnh Nghệ An', 'district' => 'Thị xã Cửa Lò'],
            ['name' => 'Phạm Thị Hương',  'email' => 'huong.pham@gmail.com',  'phone' => '0945678901', 'province' => 'Tỉnh Nghệ An', 'district' => 'Huyện Nghi Lộc'],
            ['name' => 'Võ Đình Khoa',    'email' => 'khoa.vo@gmail.com',     'phone' => '0956789012', 'province' => 'Tỉnh Nghệ An', 'district' => 'Thành phố Vinh'],
        ];
        $tenantData = [
            ['name' => 'Lê Thị Mai',      'email' => 'mai.le@gmail.com',      'phone' => '0971111111', 'gender' => 'Nữ'],
            ['name' => 'Trần Quốc Bảo',  'email' => 'bao.tran@gmail.com',    'phone' => '0972222222', 'gender' => 'Nam'],
            ['name' => 'Nguyễn Hoàng Anh','email' => 'anh.nguyen@gmail.com',  'phone' => '0973333333', 'gender' => 'Nam'],
            ['name' => 'Phạm Thùy Dung',  'email' => 'dung.pham@gmail.com',   'phone' => '0974444444', 'gender' => 'Nữ'],
            ['name' => 'Võ Minh Tuấn',    'email' => 'tuan.vo@gmail.com',     'phone' => '0975555555', 'gender' => 'Nam'],
            ['name' => 'Đặng Thị Hồng',  'email' => 'hong.dang@gmail.com',   'phone' => '0976666666', 'gender' => 'Nữ'],
            ['name' => 'Bùi Thanh Sơn',   'email' => 'son.bui@gmail.com',     'phone' => '0977777777', 'gender' => 'Nam'],
            ['name' => 'Hoàng Thị Ngọc',  'email' => 'ngoc.hoang@gmail.com',  'phone' => '0978888888', 'gender' => 'Nữ'],
        ];

        $landlords = [];
        foreach ($landlordData as $d) {
            $landlords[] = User::updateOrCreate(['email' => $d['email']], [
                'name' => $d['name'], 'phone' => $d['phone'],
                'password' => Hash::make('password'), 'role' => 'landlord',
                'province_name' => $d['province'], 'district_name' => $d['district'],
            ]);
        }

        $tenants = [];
        foreach ($tenantData as $d) {
            $tenants[] = User::updateOrCreate(['email' => $d['email']], [
                'name' => $d['name'], 'phone' => $d['phone'], 'gender' => $d['gender'],
                'password' => Hash::make('password'), 'role' => 'tenant',
                'province_name' => 'Tỉnh Nghệ An', 'district_name' => 'Thành phố Vinh',
            ]);
        }

        return [$landlords, $tenants];
    }

    // ─────────────────────────────────────────────────────────
    // 3. Rooms
    // ─────────────────────────────────────────────────────────
    private function seedRooms(array $landlords): array
    {
        $locations = [
            ['province' => 'Tỉnh Nghệ An', 'district' => 'Thành phố Vinh', 'ward' => 'Phường Hà Huy Tập',   'address' => '25 Đường Lê Lợi',         'lat' => 18.6790, 'lng' => 105.6813],
            ['province' => 'Tỉnh Nghệ An', 'district' => 'Thành phố Vinh', 'ward' => 'Phường Quang Trung',   'address' => '88 Đường Quang Trung',     'lat' => 18.6735, 'lng' => 105.6920],
            ['province' => 'Tỉnh Nghệ An', 'district' => 'Thành phố Vinh', 'ward' => 'Phường Lê Lợi',        'address' => '12 Đường Trần Phú',        'lat' => 18.6762, 'lng' => 105.6855],
            ['province' => 'Tỉnh Nghệ An', 'district' => 'Thành phố Vinh', 'ward' => 'Phường Bến Thủy',      'address' => '45 Đường Nguyễn Du',       'lat' => 18.6610, 'lng' => 105.6988],
            ['province' => 'Tỉnh Nghệ An', 'district' => 'Thành phố Vinh', 'ward' => 'Phường Trường Thi',    'address' => '10 Lê Viết Thuật',         'lat' => 18.6703, 'lng' => 105.6732],
            ['province' => 'Tỉnh Nghệ An', 'district' => 'Thành phố Vinh', 'ward' => 'Phường Hưng Bình',     'address' => '56 Nguyễn Văn Cừ',         'lat' => 18.6648, 'lng' => 105.6795],
            ['province' => 'Tỉnh Nghệ An', 'district' => 'Thị xã Cửa Lò',  'ward' => 'Phường Nghi Thu',      'address' => '22 Đường Bình Minh',       'lat' => 18.7538, 'lng' => 105.7195],
            ['province' => 'Tỉnh Nghệ An', 'district' => 'Huyện Nghi Lộc',  'ward' => 'Thị trấn Quán Hành',  'address' => '15 Đường QL1A',            'lat' => 18.7185, 'lng' => 105.7045],
        ];
        $templates = [
            ['name' => 'Phòng đơn %s',         'price' => [800000, 1200000],  'area' => [12, 16], 'amenities' => ['Wifi','Chỗ để xe','Tự do giờ giấc']],
            ['name' => 'Phòng sinh viên %s',    'price' => [1000000, 1500000], 'area' => [14, 20], 'amenities' => ['Wifi','Chỗ để xe','Camera']],
            ['name' => 'Phòng WC riêng %s',     'price' => [1500000, 2500000], 'area' => [18, 25], 'amenities' => ['Wifi','WC riêng','Nước nóng','Chỗ để xe']],
            ['name' => 'Phòng khép kín %s',     'price' => [2000000, 3200000], 'area' => [20, 30], 'amenities' => ['Wifi','WC riêng','Nước nóng','Quạt trần','Camera']],
            ['name' => 'Mini apartment %s',     'price' => [3000000, 5000000], 'area' => [25, 40], 'amenities' => ['Wifi','WC riêng','Nước nóng','Điều hòa','Bếp','Camera']],
        ];
        $desc = 'Phòng trọ sạch sẽ, thoáng mát, khu dân cư yên tĩnh, an ninh 24/7. Gần chợ, siêu thị, trường học. Chủ nhà thân thiện.';
        $rooms = [];
        $num = 200;
        foreach ($locations as $i => $loc) {
            $landlord = $landlords[$i % count($landlords)];
            $count = rand(3, 5);
            for ($r = 0; $r < $count; $r++) {
                $num++;
                $tpl = $templates[array_rand($templates)];
                $price = round(rand($tpl['price'][0], $tpl['price'][1]) / 100000) * 100000;
                $status = $r < 2 ? 'rented' : 'available';
                $rooms[] = Room::updateOrCreate(
                    ['name' => sprintf($tpl['name'], $num), 'landlord_id' => $landlord->id],
                    [
                        'price'             => $price,
                        'area'              => rand($tpl['area'][0], $tpl['area'][1]),
                        'floor'             => rand(1, 4),
                        'description'       => $desc,
                        'amenities'         => $tpl['amenities'],
                        'electricity_price' => [3500, 3800, 4000][rand(0, 2)],
                        'water_price'       => [15000, 18000, 20000][rand(0, 2)],
                        'service_fee'       => [0, 30000, 50000, 100000][rand(0, 3)],
                        'status'            => $status,
                        'approval_status'   => ($r === 0 && $i < 3) ? 'pending' : 'approved',
                        'province_name'     => $loc['province'],
                        'district_name'     => $loc['district'],
                        'ward_name'         => $loc['ward'],
                        'address_detail'    => $loc['address'],
                        'latitude'          => $loc['lat'] + (rand(-20, 20) / 10000),
                        'longitude'         => $loc['lng'] + (rand(-20, 20) / 10000),
                    ]
                );
            }
        }
        return $rooms;
    }

    // ─────────────────────────────────────────────────────────
    // 4. Yêu cầu thuê
    // ─────────────────────────────────────────────────────────
    private function seedRentRequests(array $rooms, array $tenants): void
    {
        $availableRooms = array_filter($rooms, fn($r) => $r->status === 'available');
        $notes = [
            'Tôi muốn xem phòng vào cuối tuần này, xin cho biết giờ xem phòng.',
            'Phòng còn trống không? Tôi cần dọn vào ngay tháng tới.',
            'Tôi là sinh viên năm 3, muốn thuê lâu dài. Xin liên hệ.',
            'Gia đình 2 người, muốn hỏi thêm về giá và hợp đồng.',
            'Nhờ chủ nhà xác nhận còn phòng không, tôi liên hệ ngay.',
        ];
        $statuses = ['pending', 'pending', 'pending', 'approved', 'rejected'];
        $ri = 0;
        foreach (array_slice($availableRooms, 0, 12) as $room) {
            $tenant = $tenants[$ri % count($tenants)];
            RentRequest::updateOrCreate(
                ['room_id' => $room->id, 'user_id' => $tenant->id],
                [
                    'note'         => $notes[array_rand($notes)],
                    'status'       => $statuses[array_rand($statuses)],
                    'requested_at' => now()->subDays(rand(1, 30)),
                ]
            );
            $ri++;
        }
    }

    // ─────────────────────────────────────────────────────────
    // 5. Hợp đồng + Điện nước + Hóa đơn
    // ─────────────────────────────────────────────────────────
    private function seedContracts(array $rooms, array $tenants): array
    {
        $rentedRooms = array_filter($rooms, fn($r) => $r->status === 'rented');
        $contracts = [];
        $ti = 0;

        foreach ($rentedRooms as $room) {
            $tenant = $tenants[$ti % count($tenants)];
            $ti++;
            $start = now()->subMonths(rand(2, 8));

            $contract = Contract::updateOrCreate(
                ['room_id' => $room->id, 'user_id' => $tenant->id],
                [
                    'start_date'   => $start,
                    'end_date'     => $start->copy()->addYear(),
                    'deposit'      => $room->price * 2,
                    'monthly_rent' => $room->price,
                    'status'       => 'active',
                    'notes'        => 'Hợp đồng thuê phòng trọ, bao gồm tiền điện nước hàng tháng.',
                ]
            );

            // 3 tháng điện nước + hóa đơn
            for ($m = 2; $m >= 0; $m--) {
                $date = now()->subMonths($m);
                $elecUsage = rand(50, 200);
                $waterUsage = rand(3, 15);
                $elecFee = $elecUsage * $room->electricity_price;
                $waterFee = $waterUsage * $room->water_price;
                $total = $room->price + $elecFee + $waterFee + $room->service_fee;

                Utility::updateOrCreate(
                    ['room_id' => $room->id, 'month' => $date->month, 'year' => $date->year],
                    [
                        'electricity_old'    => $oldElec = rand(100, 500),
                        'electricity_new'    => $oldElec + $elecUsage,
                        'electricity_price'  => $room->electricity_price,
                        'electricity_amount' => $elecFee,
                        'water_old'          => $oldWater = rand(10, 50),
                        'water_new'          => $oldWater + $waterUsage,
                        'water_price'        => $room->water_price,
                        'water_amount'       => $waterFee,
                    ]
                );

                $isPaid  = $m > 0;
                $methods = ['Tiền mặt', 'Chuyển khoản', 'MoMo', 'VietQR'];
                Invoice::updateOrCreate(
                    ['room_id' => $room->id, 'month' => $date->month, 'year' => $date->year],
                    [
                        'contract_id'     => $contract->id,
                        'room_fee'        => $room->price,
                        'electricity_fee' => $elecFee,
                        'water_fee'       => $waterFee,
                        'service_fee'     => $room->service_fee,
                        'total_amount'    => $total,
                        'status'          => $isPaid ? 'paid' : ($m === 0 ? 'unpaid' : 'overdue'),
                        'payment_method'  => $isPaid ? $methods[array_rand($methods)] : null,
                        'transaction_id'  => 'INV-' . strtoupper(Str::random(8)),
                        'due_date'        => $date->copy()->endOfMonth(),
                        'paid_at'         => $isPaid ? $date->copy()->addDays(rand(5, 20)) : null,
                    ]
                );
            }

            $contracts[] = $contract;
        }

        return $contracts;
    }

    // ─────────────────────────────────────────────────────────
    // 6. Yêu cầu bảo trì
    // ─────────────────────────────────────────────────────────
    private function seedMaintenance(array $rooms, array $tenants): void
    {
        $issues = [
            ['title' => 'Bóng đèn phòng bị cháy',           'desc' => 'Bóng đèn phòng ngủ bị cháy, cần thay mới.',                 'priority' => 'low',    'status' => 'done'],
            ['title' => 'Vòi nước bị rỉ',                    'desc' => 'Vòi nước trong nhà vệ sinh bị rỉ nước liên tục.',           'priority' => 'medium', 'status' => 'in_progress'],
            ['title' => 'Cửa ra vào khó đóng',               'desc' => 'Khóa cửa bị lỏng, khó mở và đóng.',                        'priority' => 'medium', 'status' => 'pending'],
            ['title' => 'Điều hòa không lạnh',               'desc' => 'Điều hòa bật lên nhưng không ra hơi lạnh.',                 'priority' => 'high',   'status' => 'pending'],
            ['title' => 'Tường bị ẩm mốc',                   'desc' => 'Góc tường gần cửa sổ bị ẩm, có mốc xanh.',                  'priority' => 'medium', 'status' => 'pending'],
            ['title' => 'Đường ống nước tầng 2 bị tắc',      'desc' => 'Nước không thoát được, xin sửa gấp.',                      'priority' => 'urgent', 'status' => 'in_progress'],
            ['title' => 'Camera tầng 1 không hoạt động',     'desc' => 'Camera an ninh tầng 1 bị mờ, hình ảnh không rõ.',          'priority' => 'low',    'status' => 'done'],
            ['title' => 'Ổ điện trong phòng bị cháy khét',   'desc' => 'Ổ điện gần bàn học có mùi khét, cần kiểm tra ngay.',       'priority' => 'urgent', 'status' => 'pending'],
        ];

        $ri = 0;
        foreach (array_slice($rooms, 0, count($issues) + 2) as $room) {
            if ($ri >= count($issues)) break;
            $issue   = $issues[$ri];
            $tenant  = $tenants[$ri % count($tenants)];
            MaintenanceRequest::updateOrCreate(
                ['room_id' => $room->id, 'user_id' => $tenant->id, 'title' => $issue['title']],
                [
                    'description' => $issue['desc'],
                    'priority'    => $issue['priority'],
                    'status'      => $issue['status'],
                    'admin_note'  => $issue['status'] === 'done' ? 'Đã xử lý xong, phòng hoạt động bình thường.' : null,
                    'resolved_at' => $issue['status'] === 'done' ? now()->subDays(rand(1, 10)) : null,
                ]
            );
            $ri++;
        }
    }

    // ─────────────────────────────────────────────────────────
    // 7. Phí hoa hồng admin
    // ─────────────────────────────────────────────────────────
    private function seedCommissions(array $contracts): void
    {
        $rate = 0.05; // 5%
        foreach ($contracts as $contract) {
            $invoices = Invoice::where('room_id', $contract->room_id)
                ->where('status', 'paid')->get();
            foreach ($invoices as $inv) {
                $amount = $inv->total_amount * $rate;
                AdminCommission::updateOrCreate(
                    ['invoice_id' => $inv->id],
                    [
                        'landlord_id' => $contract->room->landlord_id,
                        'amount'      => $amount,
                        'rate'        => $rate,
                        'status'      => rand(0, 1) ? 'paid' : 'pending',
                    ]
                );
            }
        }
    }
}
