<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Province;
use App\Models\Ward;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    /**
     * GET /api/regions/provinces
     * Trả về danh sách tỉnh/thành
     */
    public function provinces()
    {
        $provinces = Province::orderBy('name')->get(['code', 'name', 'type']);
        return response()->json($provinces);
    }

    /**
     * GET /api/regions/districts/{provinceCode}
     * Trả về danh sách quận/huyện theo tỉnh
     */
    public function districts(string $code)
    {
        $districts = District::where('province_code', $code)
            ->orderBy('name')
            ->get(['code', 'name', 'type', 'province_code']);
        return response()->json($districts);
    }

    /**
     * GET /api/regions/wards/{districtCode}
     * Trả về danh sách phường/xã theo quận/huyện
     */
    public function wards(string $code)
    {
        $wards = Ward::where('district_code', $code)
            ->orderBy('name')
            ->get(['code', 'name', 'type', 'district_code', 'province_code']);
        return response()->json($wards);
    }

    /**
     * GET /api/regions/search?q=Nghệ An&level=province
     * Tìm kiếm địa danh theo tên (dùng cho reverse geocode)
     */
    public function search(Request $request)
    {
        $q     = trim($request->get('q', ''));
        $level = $request->get('level', 'province'); // province | district | ward

        // Bỏ prefix hành chính VN trước khi tìm
        $clean = preg_replace('/^(tỉnh|thành phố|tp\.?|thị xã|thị trấn|quận|huyện|phường|xã)\s+/iu', '', $q);
        $clean = trim($clean);

        if (strlen($clean) < 2) {
            return response()->json([]);
        }

        $results = match($level) {
            'district' => District::where('name', 'LIKE', "%{$clean}%")
                ->orderByRaw("CHAR_LENGTH(name)")
                ->limit(10)->get(['code', 'name', 'province_code']),

            'ward' => Ward::where('name', 'LIKE', "%{$clean}%")
                ->orderByRaw("CHAR_LENGTH(name)")
                ->limit(10)->get(['code', 'name', 'district_code', 'province_code']),

            default => Province::where('name', 'LIKE', "%{$clean}%")
                ->orderByRaw("CHAR_LENGTH(name)")
                ->limit(5)->get(['code', 'name']),
        };

        return response()->json($results);
    }
}
