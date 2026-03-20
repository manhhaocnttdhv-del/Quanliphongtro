<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::with('images')->where('status', 'available')->approved();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('province')) {
            $query->where('province_name', $request->province);
        } elseif (!$request->has('province')) {
            // Mặc định theo tỉnh của user đang đăng nhập → cài đặt → Nghệ An
            $defaultProvince = auth()->check() && auth()->user()->province_name
                ? auth()->user()->province_name
                : (\App\Models\Setting::get('default_province', 'Tỉnh Nghệ An'));
            $query->where('province_name', $defaultProvince);
        }
        // Nếu province='' (Tất cả) thì không lọc


        if ($request->filled('district')) {
            $query->where('district_name', $request->district);
        }

        if ($request->filled('ward')) {
            $query->where('ward_name', $request->ward);
        }

        $rooms = $query->latest()->paginate(9)->withQueryString();

        return view('rooms.index', compact('rooms'));
    }

    /**
     * Returns ALL matching rooms with coordinates as JSON (for map view)
     */
    public function mapRooms(Request $request)
    {
        $query = Room::with('images')
            ->where('status', 'available')
            ->approved()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        if ($request->filled('province')) {
            $query->where('province_name', $request->province);
        } elseif (!$request->has('province')) {
            $defaultProvince = auth()->check() && auth()->user()->province_name
                ? auth()->user()->province_name
                : (\App\Models\Setting::get('default_province', 'Tỉnh Nghệ An'));
            $query->where('province_name', $defaultProvince);
        }
        if ($request->filled('district')) {
            $query->where('district_name', $request->district);
        }
        if ($request->filled('ward')) {
            $query->where('ward_name', $request->ward);
        }

        $rooms = $query->latest()->limit(2000)->get();

        return response()->json($rooms->map(function ($r) {
            return [
                'id'      => $r->id,
                'name'    => $r->name,
                'price'   => number_format($r->price) . 'đ/tháng',
                'lat'     => $r->latitude,
                'lng'     => $r->longitude,
                'status'  => $r->statusLabel(),
                'badge'   => $r->statusBadge(),
                'address' => $r->fullAddress(),
                'img'     => $r->images->first()
                    ? asset('storage/' . $r->images->first()->image_path)
                    : null,
                'url'     => route('rooms.show', $r),
            ];
        }));
    }


    public function show(Room $room)
    {
        $room->load(['images', 'landlord', 'reviews.user']);
        $hasActiveRequest = false;
        $userReview = null;
        if (auth()->check()) {
            $hasActiveRequest = $room->rentRequests()
                ->where('user_id', auth()->id())
                ->where('status', 'pending')
                ->exists();
            $userReview = $room->reviews()->where('user_id', auth()->id())->first();
        }

        return view('rooms.show', compact('room', 'hasActiveRequest', 'userReview'));
    }
}
