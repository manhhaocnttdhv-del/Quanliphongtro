<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\MaintenanceRequest;
use App\Models\Room;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $query = MaintenanceRequest::with(['room', 'user'])->latest();

        // If landlord, only show requests for their rooms
        if (auth()->user()->isLandlord()) {
            $query->whereHas('room', function($q) {
                $q->where('landlord_id', auth()->id());
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->paginate(15);
        return view('admin.maintenance.index', compact('requests'));
    }

    public function show(MaintenanceRequest $maintenance)
    {
        // Check permission if landlord
        if (auth()->user()->isLandlord() && $maintenance->room->landlord_id !== auth()->id()) {
            abort(403);
        }

        return view('admin.maintenance.show', compact('maintenance'));
    }

    public function update(Request $request, MaintenanceRequest $maintenance)
    {
        // Check permission if landlord
        if (auth()->user()->isLandlord() && $maintenance->room->landlord_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress,resolved,cancelled',
            'priority' => 'required|in:low,medium,high',
            'admin_notes' => 'nullable|string',
        ]);

        $maintenance->update($request->only(['status', 'priority', 'admin_notes']));

        return redirect()->route('admin.maintenance.show', $maintenance)
            ->with('success', 'Cập nhật trạng thái yêu cầu bảo trì thành công.');
    }
}
