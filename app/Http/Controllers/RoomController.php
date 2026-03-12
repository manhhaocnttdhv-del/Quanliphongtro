<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::with('images')->where('status', 'available');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('province')) {
            $query->where('province_name', $request->province);
        }

        if ($request->filled('district')) {
            $query->where('district_name', $request->district);
        }

        if ($request->filled('ward')) {
            $query->where('ward_name', $request->ward);
        }

        $rooms = $query->latest()->paginate(9)->withQueryString();

        return view('rooms.index', compact('rooms'));
    }

    public function show(Room $room)
    {
        $room->load(['images', 'landlord']);
        $hasActiveRequest = false;
        if (auth()->check()) {
            $hasActiveRequest = $room->rentRequests()
                ->where('user_id', auth()->id())
                ->where('status', 'pending')
                ->exists();
        }

        return view('rooms.show', compact('room', 'hasActiveRequest'));
    }
}
