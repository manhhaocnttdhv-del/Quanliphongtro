<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\RoomImage;
use App\Models\User;
use App\Models\Contract;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $landlord = User::where('role', 'landlord')->first();
        if (!$landlord) return;

        $rooms = [
            [
                'data' => [
                    'landlord_id'       => $landlord->id,
                    'name'              => 'Phòng 101',
                    'price'             => 2500000,
                    'area'              => 20,
                    'floor'             => 1,
                    'description'       => 'Phòng rộng rãi, thoáng mát, có ban công nhỏ. Gần chợ và siêu thị. Có gác xép. Nội thất cơ bản: giường, tủ, bàn học. Khu vực an ninh, có camera giám sát 24/7.',
                    'amenities'         => ['Wifi', 'Gác xép', 'Ban công', 'Camera an ninh', 'Chỗ để xe'],
                    'electricity_price' => 3500,
                    'water_price'       => 15000,
                    'service_fee'       => 50000,
                    'status'            => 'available',
                ],
                'images' => ['rooms/room-101-1.jpg', 'rooms/room-101-2.jpg', 'rooms/room-101-3.jpg'],
            ],
            [
                'data' => [
                    'landlord_id'       => $landlord->id,
                    'name'              => 'Phòng 102',
                    'price'             => 3000000,
                    'area'              => 25,
                    'floor'             => 1,
                    'description'       => 'Phòng có nhà vệ sinh riêng, máy lạnh, nước nóng. Yên tĩnh, phù hợp cho đôi bạn hoặc sinh viên. Cửa sổ thoáng, ánh sáng tự nhiên tốt.',
                    'amenities'         => ['Wifi', 'Máy lạnh', 'Nước nóng', 'WC riêng', 'Máy giặt chung'],
                    'electricity_price' => 3500,
                    'water_price'       => 15000,
                    'service_fee'       => 50000,
                    'status'            => 'available',
                ],
                'images' => ['rooms/room-102-1.jpg', 'rooms/room-102-2.jpg', 'rooms/room-102-3.jpg'],
            ],
            [
                'data' => [
                    'landlord_id'       => $landlord->id,
                    'name'              => 'Phòng 201',
                    'price'             => 3500000,
                    'area'              => 30,
                    'floor'             => 2,
                    'description'       => 'Phòng cao cấp tầng 2, view đẹp nhìn ra đường. Có máy lạnh, tủ lạnh, bếp điện. Đầy đủ tiện nghi, nội thất hiện đại.',
                    'amenities'         => ['Wifi', 'Máy lạnh', 'Tủ lạnh', 'Bếp điện', 'Nội thất đầy đủ', 'WC riêng'],
                    'electricity_price' => 3500,
                    'water_price'       => 15000,
                    'service_fee'       => 100000,
                    'status'            => 'rented',
                ],
                'images' => ['rooms/room-201-1.jpg', 'rooms/room-201-2.jpg', 'rooms/room-201-3.jpg'],
            ],
            [
                'data' => [
                    'landlord_id'       => $landlord->id,
                    'name'              => 'Phòng 202',
                    'price'             => 2800000,
                    'area'              => 22,
                    'floor'             => 2,
                    'description'       => 'Phòng tầng 2, thoáng mát. Có gác xép tiện lợi, phù hợp cho 1-2 người. Giá ưu đãi cho sinh viên. Gần trường đại học.',
                    'amenities'         => ['Wifi', 'Gác xép', 'Gần trường ĐH', 'Chỗ để xe', 'Tự do giờ giấc'],
                    'electricity_price' => 3500,
                    'water_price'       => 15000,
                    'service_fee'       => 50000,
                    'status'            => 'available',
                ],
                'images' => ['rooms/room-202-1.jpg', 'rooms/room-202-2.jpg', 'rooms/room-202-3.jpg'],
            ],
        ];

        foreach ($rooms as $roomEntry) {
            $room = Room::updateOrCreate(
                ['name' => $roomEntry['data']['name'], 'landlord_id' => $roomEntry['data']['landlord_id']],
                $roomEntry['data']
            );

            // Add images if the room was just created (no images yet)
            if ($room->images()->count() === 0) {
                foreach ($roomEntry['images'] as $i => $imagePath) {
                    $fullPath = storage_path('app/public/' . $imagePath);
                    if (file_exists($fullPath)) {
                        RoomImage::create([
                            'room_id'    => $room->id,
                            'image_path' => $imagePath,
                            'is_primary' => $i === 0,
                        ]);
                    }
                }
            }
        }

        // Create a sample tenant for room 201 (rented)
        $sampleTenant = User::updateOrCreate(
            ['email' => 'nguoithue@gmail.com'],
            [
                'name'           => 'Nguyễn Văn A',
                'phone'          => '0987654321',
                'id_card'        => '012345678901',
                'dob'            => '2000-01-01',
                'gender'         => 'Nam',
                'province_name'  => 'Thành phố Hà Nội',
                'district_name'  => 'Quận Cầu Giấy',
                'ward_name'      => 'Phường Dịch Vọng',
                'address_detail' => 'Số 10 ngõ 20',
                'password'       => Hash::make('password'),
                'role'           => 'tenant',
            ]
        );

        $room201 = Room::where('name', 'Phòng 201')->first();
        if ($room201) {
            Contract::updateOrCreate(
                ['room_id' => $room201->id, 'user_id' => $sampleTenant->id],
                [
                    'start_date'   => now()->subMonths(3),
                    'end_date'     => now()->addMonths(9),
                    'deposit'      => 7000000,
                    'monthly_rent' => $room201->price,
                    'status'       => 'active',
                ]
            );
        }
    }
}
