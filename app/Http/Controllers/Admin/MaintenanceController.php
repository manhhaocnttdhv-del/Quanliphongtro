<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $user  = auth()->user();
        $query = MaintenanceRequest::with(['room', 'user']);

        if ($user->isLandlord()) {
            $query->whereHas('room', fn($q) => $q->where('landlord_id', $user->id));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $requests = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'pending'     => MaintenanceRequest::when($user->isLandlord(), fn($q) =>
                                $q->whereHas('room', fn($r) => $r->where('landlord_id', $user->id)))
                                ->where('status', 'pending')->count(),
            'in_progress' => MaintenanceRequest::when($user->isLandlord(), fn($q) =>
                                $q->whereHas('room', fn($r) => $r->where('landlord_id', $user->id)))
                                ->where('status', 'in_progress')->count(),
            'done'        => MaintenanceRequest::when($user->isLandlord(), fn($q) =>
                                $q->whereHas('room', fn($r) => $r->where('landlord_id', $user->id)))
                                ->where('status', 'done')->count(),
        ];

        return view('admin.maintenance.index', compact('requests', 'stats'));
    }

    public function show(MaintenanceRequest $maintenance)
    {
        if (auth()->user()->isLandlord() && $maintenance->room->landlord_id !== auth()->id()) {
            abort(403);
        }

        $maintenance->load(['room', 'user']);
        return view('admin.maintenance.show', compact('maintenance'));
    }

    public function updateStatus(Request $request, MaintenanceRequest $maintenance)
    {
        if (auth()->user()->isLandlord() && $maintenance->room->landlord_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'status'     => 'required|in:pending,in_progress,done,rejected',
            'admin_note' => 'nullable|string|max:1000',
        ]);

        $data = [
            'status'     => $request->status,
            'admin_note' => $request->admin_note,
        ];

        if ($request->status === 'done') {
            $data['resolved_at'] = now();
        }

        $maintenance->update($data);

        return back()->with('success', 'Cập nhật trạng thái yêu cầu bảo trì thành công!');
    }
}
