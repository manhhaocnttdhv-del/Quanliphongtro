<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Contract::with(['user', 'room']);

        if ($user->isLandlord()) {
            $query->whereHas('room', function($q) use ($user) {
                $q->where('landlord_id', $user->id);
            });
        }

        $contracts = $query->latest()->paginate(15);

        return view('admin.contracts.index', compact('contracts'));
    }

    public function show(Contract $contract)
    {
        // Authorization check
        if (auth()->user()->isLandlord() && $contract->room->landlord_id !== auth()->id()) {
            abort(403);
        }

        $contract->load(['user', 'room', 'invoices']);
        return view('admin.contracts.show', compact('contract'));
    }

    public function endContract(Contract $contract)
    {
        // Authorization check
        if (auth()->user()->isLandlord() && $contract->room->landlord_id !== auth()->id()) {
            abort(403);
        }

        $contract->update(['status' => 'ended']);
        $contract->room->update(['status' => 'available']);

        return back()->with('success', 'Hợp đồng đã kết thúc. Phòng đã được đặt về trạng thái trống.');
    }
}
