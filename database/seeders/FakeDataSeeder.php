<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Room;
use App\Models\RoomImage;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Utility;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FakeDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🏠 Seeding landlords...');
        $landlords = $this->seedLandlords();

        $this->command->info('👨‍💻 Seeding staff...');
        $this->seedStaff();

        $this->command->info('🏢 Seeding rooms...');
        $rooms = $this->seedRooms($landlords);

        $this->command->info('👤 Seeding tenants + contracts...');
        $this->seedTenantsAndContracts($rooms);

        $this->command->info('✅ Done! Created ' . count($landlords) . ' landlords, ' . count($rooms) . ' rooms.');
    }

    private function seedLandlords(): array
    {
        $data = [
            ['name' => 'Trần Văn Minh',    'email' => 'minh.tran@gmail.com',   'phone' => '0912345678', 'province' => 'Tỉnh Nghệ An', 'district' => 'Thành phố Vinh'],
            ['name' => 'Nguyễn Thị Lan',    'email' => 'lan.nguyen@gmail.com',  'phone' => '0923456789', 'province' => 'Tỉnh Nghệ An', 'district' => 'Thành phố Vinh'],
            ['name' => 'Lê Hoàng Phúc',     'email' => 'phuc.le@gmail.com',     'phone' => '0934567890', 'province' => 'Tỉnh Nghệ An', 'district' => 'Thị xã Cửa Lò'],
            ['name' => 'Phạm Thị Hương',    'email' => 'huong.pham@gmail.com',  'phone' => '0945678901', 'province' => 'Tỉnh Nghệ An', 'district' => 'Huyện Nghi Lộc'],
            ['name' => 'Võ Đình Khoa',       'email' => 'khoa.vo@gmail.com',     'phone' => '0956789012', 'province' => 'Tỉnh Nghệ An', 'district' => 'Thành phố Vinh'],
        ];

        $landlords = [];
        foreach ($data as $d) {
            $landlords[] = User::updateOrCreate(
                ['email' => $d['email']],
                [
                    'name'          => $d['name'],
                    'phone'         => $d['phone'],
                    'password'      => Hash::make('password'),
                    'role'          => 'landlord',
                    'province_name' => $d['province'],
                    'district_name' => $d['district'],
                ]
            );
        }
        return $landlords;
    }

    private function seedStaff(): void
    {
        User::updateOrCreate(
            ['email' => 'staff@phongtro.com'],
            [
                'name'     => 'Nhân viên Tuấn',
                'phone'    => '0967890123',
                'password' => Hash::make('password'),
                'role'     => 'staff',
            ]
        );
    }

    private function seedRooms(array $landlords): array
    {
        // Tọa độ thực tế tại Nghệ An - lấy từ Google Maps
        $locations = [
            // === TP. Vinh - Khu vực trung tâm ===
            ['province' => 'Tỉnh Nghệ An', 'district' => 'Thành phố Vinh', 'ward' => 'Phường Hà Huy Tập',      'address' => '25 Đường Lê Lợi',               'lat' => 18.6790, 'lng' => 105.6813],
            ['province' => 'Tỉnh Nghệ An', 'district' => 'Thành phố Vinh', 'ward' => 'Phường Quang Trung',      'address' => '88 Đường Quang Trung',           'lat' => 18.6735, 'lng' => 105.6920],
            ['province' => 'Tỉnh Nghệ An', 'district' => 'Thành phố Vinh', 'ward' => 'Phường Lê Lợi',           'address' => '12 Đường Trần Phú',              'lat' => 18.6762, 'lng' => 105.6855],
            ['province' => 'Tỉnh Nghệ An', 'district' => 'Thành phố Vinh', 'ward' => 'Phường Bến Thủy',         'address' => '45 Đường Nguyễn Du',             'lat' => 18.6610, 'lng' => 105.6988],

            // === TP. Vinh - Gần trường ĐH Vinh ===
            ['province' => 'Tỉnh Nghệ An', 'district' => 'Thành phố Vinh', 'ward' => 'Phường Trường Thi',       'address' => '10 Đường Lê Viết Thuật',         'lat' => 18.6703, 'lng' => 105.6732],
            ['province' => 'Tỉnh Nghệ An', 'district' => 'Thành phố Vinh', 'ward' => 'Phường Trường Thi',       'address' => '33 Ngõ 5 Lê Viết Thuật',         'lat' => 18.6715, 'lng' => 105.6718],
            ['province' => 'Tỉnh Nghệ An', 'district' => 'Thành phố Vinh', 'ward' => 'Phường Hưng Bình',        'address' => '56 Đường Nguyễn Văn Cừ',         'lat' => 18.6648, 'lng' => 105.6795],

            // === TP. Vinh - Khu vực khác ===
            ['province' => 'Tỉnh Nghệ An', 'district' => 'Thành phố Vinh', 'ward' => 'Phường Cửa Nam',          'address' => '78 Đường Hồ Tùng Mậu',          'lat' => 18.6825, 'lng' => 105.6750],
            ['province' => 'Tỉnh Nghệ An', 'district' => 'Thành phố Vinh', 'ward' => 'Phường Đội Cung',         'address' => '20 Đường Nguyễn Thị Minh Khai',  'lat' => 18.6680, 'lng' => 105.6870],
            ['province' => 'Tỉnh Nghệ An', 'district' => 'Thành phố Vinh', 'ward' => 'Phường Hưng Phúc',        'address' => '150 Đường Lê Nin',               'lat' => 18.6755, 'lng' => 105.6780],
            ['province' => 'Tỉnh Nghệ An', 'district' => 'Thành phố Vinh', 'ward' => 'Phường Trung Đô',         'address' => '30 Đường Phong Định Cảng',       'lat' => 18.6695, 'lng' => 105.6945],

            // === Thị xã Cửa Lò ===
            ['province' => 'Tỉnh Nghệ An', 'district' => 'Thị xã Cửa Lò', 'ward' => 'Phường Nghi Thu',         'address' => '22 Đường Bình Minh',             'lat' => 18.7538, 'lng' => 105.7195],
            ['province' => 'Tỉnh Nghệ An', 'district' => 'Thị xã Cửa Lò', 'ward' => 'Phường Thu Thủy',         'address' => '55 Đường Nguyễn Sinh Cung',      'lat' => 18.7610, 'lng' => 105.7280],

            // === Huyện Nghi Lộc ===
            ['province' => 'Tỉnh Nghệ An', 'district' => 'Huyện Nghi Lộc', 'ward' => 'Thị trấn Quán Hành',     'address' => '15 Đường QL1A',                  'lat' => 18.7185, 'lng' => 105.7045],
        ];

        $roomTemplates = [
            ['name' => 'Phòng %s',             'price' => [800000, 1200000],  'area' => [12, 16], 'desc' => 'Phòng trọ đơn giản, sạch sẽ, phù hợp 1 người. WC chung, gần chợ và trường học. An ninh tốt, có chỗ để xe.', 'amenities' => ['Wifi', 'Chỗ để xe', 'Tự do giờ giấc']],
            ['name' => 'Phòng %s',             'price' => [1200000, 1800000], 'area' => [16, 22], 'desc' => 'Phòng trọ rộng rãi, thoáng mát, có cửa sổ. WC chung sạch sẽ. Gần ĐH Vinh, tiện đi lại. Giá sinh viên.', 'amenities' => ['Wifi', 'Chỗ để xe', 'Camera an ninh', 'Tự do giờ giấc']],
            ['name' => 'Phòng %s - Có gác',   'price' => [1000000, 1500000], 'area' => [14, 20], 'desc' => 'Phòng trọ có gác xép rộng, tiết kiệm diện tích. WC chung. Phù hợp sinh viên, gần trường ĐH Vinh và chợ.', 'amenities' => ['Wifi', 'Gác xép', 'Chỗ để xe', 'Tự do giờ giấc']],
            ['name' => 'Phòng %s - WC riêng',  'price' => [1500000, 2500000], 'area' => [18, 25], 'desc' => 'Phòng trọ khép kín WC riêng, có nước nóng. Khu dân cư yên tĩnh, an ninh. Gần chợ, siêu thị và trường học.', 'amenities' => ['Wifi', 'WC riêng', 'Nước nóng', 'Chỗ để xe', 'Camera an ninh']],
            ['name' => 'Phòng %s - Khép kín',  'price' => [2000000, 3000000], 'area' => [20, 28], 'desc' => 'Phòng trọ khép kín đầy đủ tiện nghi: WC riêng, nước nóng, quạt trần. Thoáng mát, sạch sẽ. Gần trung tâm TP Vinh.', 'amenities' => ['Wifi', 'WC riêng', 'Nước nóng', 'Quạt trần', 'Chỗ để xe', 'Camera an ninh']],
        ];

        $statuses = ['available', 'available', 'available', 'rented', 'available'];
        $elecPrices = [3500, 3800, 4000];
        $waterPrices = [15000, 18000, 20000];
        $serviceFees = [0, 30000, 50000, 80000, 100000];

        $rooms = [];
        $roomNum = 100;

        foreach ($locations as $locIdx => $loc) {
            $landlord = $landlords[$locIdx % count($landlords)];
            $numRooms = rand(2, 4);

            for ($r = 0; $r < $numRooms; $r++) {
                $roomNum++;
                $template = $roomTemplates[array_rand($roomTemplates)];
                $floor = rand(1, 5);
                $price = rand($template['price'][0], $template['price'][1]);
                $price = round($price / 100000) * 100000;

                $room = Room::updateOrCreate(
                    ['name' => sprintf($template['name'], $roomNum), 'landlord_id' => $landlord->id],
                    [
                        'price'             => $price,
                        'area'              => rand($template['area'][0], $template['area'][1]),
                        'floor'             => $floor,
                        'description'       => $template['desc'],
                        'amenities'         => $template['amenities'],
                        'electricity_price' => $elecPrices[array_rand($elecPrices)],
                        'water_price'       => $waterPrices[array_rand($waterPrices)],
                        'service_fee'       => $serviceFees[array_rand($serviceFees)],
                        'status'            => $statuses[array_rand($statuses)],
                        'approval_status'   => 'approved',
                        'province_name'     => $loc['province'],
                        'district_name'     => $loc['district'],
                        'ward_name'         => $loc['ward'],
                        'address_detail'    => $loc['address'],
                        'latitude'          => $loc['lat'] + (rand(-30, 30) / 10000),
                        'longitude'         => $loc['lng'] + (rand(-30, 30) / 10000),
                    ]
                );

                $rooms[] = $room;
            }
        }

        // Add a few pending rooms for staff to practice
        foreach (array_slice($rooms, -5) as $pendingRoom) {
            $pendingRoom->update(['approval_status' => 'pending']);
        }

        return $rooms;
    }

    private function seedTenantsAndContracts(array $rooms): void
    {
        $tenants = [
            ['name' => 'Lê Thị Mai',       'email' => 'mai.le@gmail.com',      'phone' => '0971111111', 'gender' => 'Nữ'],
            ['name' => 'Trần Quốc Bảo',    'email' => 'bao.tran@gmail.com',    'phone' => '0972222222', 'gender' => 'Nam'],
            ['name' => 'Nguyễn Hoàng Anh',  'email' => 'anh.nguyen@gmail.com',  'phone' => '0973333333', 'gender' => 'Nam'],
            ['name' => 'Phạm Thùy Dung',   'email' => 'dung.pham@gmail.com',   'phone' => '0974444444', 'gender' => 'Nữ'],
            ['name' => 'Võ Minh Tuấn',      'email' => 'tuan.vo@gmail.com',     'phone' => '0975555555', 'gender' => 'Nam'],
            ['name' => 'Đặng Thị Hồng',    'email' => 'hong.dang@gmail.com',   'phone' => '0976666666', 'gender' => 'Nữ'],
            ['name' => 'Bùi Thanh Sơn',     'email' => 'son.bui@gmail.com',     'phone' => '0977777777', 'gender' => 'Nam'],
            ['name' => 'Hoàng Thị Ngọc',   'email' => 'ngoc.hoang@gmail.com',  'phone' => '0978888888', 'gender' => 'Nữ'],
            ['name' => 'Cao Văn Đức',       'email' => 'duc.cao@gmail.com',     'phone' => '0979999999', 'gender' => 'Nam'],
            ['name' => 'Trịnh Thị Thảo',   'email' => 'thao.trinh@gmail.com',  'phone' => '0970000000', 'gender' => 'Nữ'],
        ];

        $tenantUsers = [];
        foreach ($tenants as $t) {
            $tenantUsers[] = User::updateOrCreate(
                ['email' => $t['email']],
                [
                    'name'          => $t['name'],
                    'phone'         => $t['phone'],
                    'gender'        => $t['gender'],
                    'password'      => Hash::make('password'),
                    'role'          => 'tenant',
                    'province_name' => 'Tỉnh Nghệ An',
                    'district_name' => 'Thành phố Vinh',
                ]
            );
        }

        // Assign tenants to rented rooms
        $rentedRooms = array_filter($rooms, fn($r) => $r->status === 'rented');
        $tenantIdx = 0;

        foreach ($rentedRooms as $room) {
            if ($tenantIdx >= count($tenantUsers)) break;

            $tenant = $tenantUsers[$tenantIdx++];
            $startDate = now()->subMonths(rand(1, 6));

            $contract = Contract::updateOrCreate(
                ['room_id' => $room->id, 'user_id' => $tenant->id],
                [
                    'start_date'   => $startDate,
                    'end_date'     => $startDate->copy()->addYear(),
                    'deposit'      => $room->price * 2,
                    'monthly_rent' => $room->price,
                    'status'       => 'active',
                ]
            );

            // Create invoices for the last 3 months
            for ($m = 2; $m >= 0; $m--) {
                $invoiceDate = now()->subMonths($m);
                $elecUsage = rand(50, 200);
                $waterUsage = rand(3, 15);
                $elecFee = $elecUsage * $room->electricity_price;
                $waterFee = $waterUsage * $room->water_price;
                $total = $room->price + $elecFee + $waterFee + $room->service_fee;

                Utility::updateOrCreate(
                    ['room_id' => $room->id, 'month' => $invoiceDate->month, 'year' => $invoiceDate->year],
                    [
                        'electricity_old'   => rand(100, 500),
                        'electricity_new'   => rand(500, 900),
                        'electricity_price' => $room->electricity_price,
                        'electricity_amount'=> $elecFee,
                        'water_old'         => rand(10, 50),
                        'water_new'         => rand(50, 100),
                        'water_price'       => $room->water_price,
                        'water_amount'      => $waterFee,
                    ]
                );

                $status = $m === 0 ? 'unpaid' : 'paid';

                Invoice::updateOrCreate(
                    ['room_id' => $room->id, 'month' => $invoiceDate->month, 'year' => $invoiceDate->year],
                    [
                        'contract_id'     => $contract->id,
                        'room_fee'        => $room->price,
                        'electricity_fee' => $elecFee,
                        'water_fee'       => $waterFee,
                        'service_fee'     => $room->service_fee,
                        'total_amount'    => $total,
                        'status'          => $status,
                        'transaction_id'  => 'INV-' . strtoupper(\Illuminate\Support\Str::random(8)),
                        'due_date'        => $invoiceDate->copy()->addDays(15),
                        'paid_at'         => $status === 'paid' ? $invoiceDate->copy()->addDays(rand(5, 14)) : null,
                        'payment_method'  => $status === 'paid' ? ['Tiền mặt', 'Chuyển khoản', 'MoMo'][rand(0, 2)] : null,
                    ]
                );
            }
        }
    }
}
