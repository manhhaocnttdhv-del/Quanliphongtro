<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RentRequest;
use App\Models\User;
use App\Notifications\NewRentRequest;
use Illuminate\Http\Request;

class RentRequestController extends Controller
{
    public function create(Room $room)
    {
        if ($room->status !== 'available') {
            return redirect()->route('rooms.show', $room)
                ->with('error', 'Phòng này hiện không còn trống.');
        }

        return view('rent-requests.create', compact('room'));
    }

    public function store(Request $request, Room $room)
    {
        $request->validate([
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        // Prevent duplicate pending requests
        $existing = RentRequest::where('user_id', auth()->id())
            ->where('room_id', $room->id)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return redirect()->route('rooms.show', $room)
                ->with('error', 'Bạn đã gửi yêu cầu thuê phòng này rồi.');
        }

        $rentRequest = RentRequest::create([
            'user_id'      => auth()->id(),
            'room_id'      => $room->id,
            'note'         => $request->note,
            'status'       => 'pending',
            'requested_at' => now(),
        ]);

        // Notify all admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewRentRequest($rentRequest));
        }

        return redirect()->route('rooms.index')
            ->with('success', 'Yêu cầu thuê phòng đã được gửi! Chúng tôi sẽ liên hệ bạn sớm.');
    }
}
