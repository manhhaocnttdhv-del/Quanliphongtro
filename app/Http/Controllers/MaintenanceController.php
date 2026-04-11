<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaintenanceRequest;
use App\Models\Room;
use Illuminate\Support\Facades\Storage;

class MaintenanceController extends Controller
{
    public function index()
    {
        $requests = auth()->user()->maintenanceRequests()->with('room')->latest()->get();
        return view('maintenance.index', compact('requests'));
    }

    public function create()
    {
        // Get rooms that the user is currently renting
        $rooms = auth()->user()->contracts()
            ->where('status', 'active')
            ->with('room')
            ->get()
            ->pluck('room');

        if ($rooms->isEmpty()) {
            return redirect()->route('home')->with('error', 'Bạn không có phòng trọ nào đang thuê để báo cáo sự cố.');
        }

        return view('maintenance.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Verify user is renting this room
        $isRenting = auth()->user()->contracts()
            ->where('room_id', $request->room_id)
            ->where('status', 'active')
            ->exists();

        if (!$isRenting) {
            return back()->with('error', 'Bạn không có quyền báo cáo sự cố cho phòng này.');
        }

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('maintenance', 'public');
            }
        }

        MaintenanceRequest::create([
            'room_id' => $request->room_id,
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'images' => $imagePaths,
            'status' => 'pending',
        ]);

        return redirect()->route('maintenance.index')->with('success', 'Yêu cầu bảo trì đã được gửi thành công.');
    }

    public function show(MaintenanceRequest $maintenanceRequest)
    {
        $this->authorize('view', $maintenanceRequest);
        return view('maintenance.show', compact('maintenanceRequest'));
    }
}
