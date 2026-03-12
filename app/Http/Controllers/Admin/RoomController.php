<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Room::with('images')->withCount(['rentRequests', 'contracts']);

        if ($user->isLandlord()) {
            $query->where('landlord_id', $user->id);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $rooms = $query->latest()->paginate(10)->withQueryString();

        return view('admin.rooms.index', compact('rooms'));
    }

    public function create()
    {
        return view('admin.rooms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                => 'required|string|max:255',
            'price'               => 'required|numeric|min:0',
            'area'                => 'nullable|numeric|min:0',
            'floor'               => 'nullable|integer',
            'description'         => 'nullable|string',
            'electricity_price'   => 'required|numeric|min:0',
            'water_price'         => 'required|numeric|min:0',
            'service_fee'         => 'required|numeric|min:0',
            'images.*'            => 'nullable|image|max:5120',
            'amenities_text'      => 'nullable|string',
            'province_name'       => 'required|string',
            'district_name'       => 'required|string',
            'ward_name'           => 'required|string',
            'address_detail'      => 'nullable|string',
        ]);

        $data = $request->only([
            'name', 'price', 'area', 'floor', 'description',
            'electricity_price', 'water_price', 'service_fee',
            'province_name', 'district_name', 'ward_name', 'address_detail',
        ]);

        if (auth()->user()->isLandlord()) {
            $data['landlord_id'] = auth()->id();
        }

        if ($request->filled('amenities_text')) {
            $data['amenities'] = array_map('trim', explode(',', $request->amenities_text));
        }

        $room = Room::create($data);

        if ($request->hasFile('images')) {
            $isPrimary = true;
            foreach ($request->file('images') as $image) {
                $path = $image->store('rooms', 'public');
                RoomImage::create([
                    'room_id'    => $room->id,
                    'image_path' => $path,
                    'is_primary' => $isPrimary,
                ]);
                $isPrimary = false;
            }
        }

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Thêm phòng thành công!');
    }

    public function edit(Room $room)
    {
        // Authorization check
        if (auth()->user()->isLandlord() && $room->landlord_id !== auth()->id()) {
            abort(403);
        }

        $room->load('images');
        return view('admin.rooms.edit', compact('room'));
    }

    public function update(Request $request, Room $room)
    {
        // Authorization check
        if (auth()->user()->isLandlord() && $room->landlord_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'name'                => 'required|string|max:255',
            'price'               => 'required|numeric|min:0',
            'area'                => 'nullable|numeric|min:0',
            'floor'               => 'nullable|integer',
            'description'         => 'nullable|string',
            'electricity_price'   => 'required|numeric|min:0',
            'water_price'         => 'required|numeric|min:0',
            'service_fee'         => 'required|numeric|min:0',
            'images.*'            => 'nullable|image|max:5120',
            'province_name'       => 'required|string',
            'district_name'       => 'required|string',
            'ward_name'           => 'required|string',
            'address_detail'      => 'nullable|string',
        ]);

        $data = $request->only([
            'name', 'price', 'area', 'floor', 'description',
            'electricity_price', 'water_price', 'service_fee',
            'province_name', 'district_name', 'ward_name', 'address_detail',
        ]);

        if ($request->filled('amenities_text')) {
            $data['amenities'] = array_map('trim', explode(',', $request->amenities_text));
        } else {
            $data['amenities'] = [];
        }

        $room->update($data);

        if ($request->hasFile('images')) {
            $isPrimary = $room->images()->count() === 0;
            foreach ($request->file('images') as $image) {
                $path = $image->store('rooms', 'public');
                RoomImage::create([
                    'room_id'    => $room->id,
                    'image_path' => $path,
                    'is_primary' => $isPrimary,
                ]);
                $isPrimary = false;
            }
        }

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Cập nhật phòng thành công!');
    }

    public function destroy(Room $room)
    {
        // Authorization check
        if (auth()->user()->isLandlord() && $room->landlord_id !== auth()->id()) {
            abort(403);
        }

        foreach ($room->images as $img) {
            Storage::disk('public')->delete($img->image_path);
        }
        $room->delete();

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Đã xoá phòng!');
    }

    public function destroyImage(RoomImage $image)
    {
        // Authorization check
        if (auth()->user()->isLandlord() && $image->room->landlord_id !== auth()->id()) {
            abort(403);
        }

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return back()->with('success', 'Đã xoá ảnh!');
    }
}
