<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaintenanceController extends Controller
{
    public function index()
    {
        $user     = auth()->user();
        $requests = MaintenanceRequest::with('room')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('user.maintenance.index', compact('requests'));
    }

    public function create()
    {
        $user = auth()->user();

        // Get rooms the user is currently renting (active contracts)
        $activeContracts = Contract::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('room')
            ->get();

        $rooms = $activeContracts->pluck('room')->filter();

        if ($rooms->isEmpty()) {
            return redirect()->route('rooms.index')
                ->with('error', 'Bạn cần có hợp đồng thuê phòng đang hiệu lực để gửi yêu cầu bảo trì.');
        }

        return view('user.maintenance.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_id'     => 'required|exists:rooms,id',
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'priority'    => 'required|in:low,medium,high,urgent',
            'images'      => 'nullable|array|max:5',
            'images.*'    => 'image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        // Verify user has active contract for this room
        $hasContract = Contract::where('user_id', auth()->id())
            ->where('room_id', $request->room_id)
            ->where('status', 'active')
            ->exists();

        if (!$hasContract) {
            return back()->with('error', 'Bạn không có quyền gửi yêu cầu bảo trì cho phòng này.');
        }

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('maintenance', 'public');
            }
        }

        MaintenanceRequest::create([
            'room_id'     => $request->room_id,
            'user_id'     => auth()->id(),
            'title'       => $request->title,
            'description' => $request->description,
            'priority'    => $request->priority,
            'images'      => $imagePaths ?: null,
        ]);

        return redirect()->route('maintenance.index')
            ->with('success', 'Yêu cầu bảo trì đã được gửi thành công! Chúng tôi sẽ phản hồi sớm nhất.');
    }
}
