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
        $user  = auth()->user();
        $query = Contract::with(['user', 'room']);

        if ($user->isLandlord()) {
            $query->whereHas('room', fn($q) => $q->where('landlord_id', $user->id));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $contracts = $query->latest()->paginate(15)->withQueryString();

        return view('admin.contracts.index', compact('contracts'));
    }

    public function create()
    {
        $user          = auth()->user();
        $availableRooms = Room::where('status', 'available')
            ->when($user->isLandlord(), fn($q) => $q->where('landlord_id', $user->id))
            ->get();

        $tenants = User::where('role', 'tenant')->orderBy('name')->get();

        return view('admin.contracts.create', compact('availableRooms', 'tenants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'      => 'required|exists:users,id',
            'room_id'      => 'required|exists:rooms,id',
            'start_date'   => 'required|date',
            'end_date'     => 'nullable|date|after:start_date',
            'deposit'      => 'required|numeric|min:0',
            'monthly_rent' => 'required|numeric|min:0',
            'notes'        => 'nullable|string',
        ]);

        $room = Room::findOrFail($request->room_id);

        // Authorization check
        if (auth()->user()->isLandlord() && $room->landlord_id !== auth()->id()) {
            abort(403);
        }

        if (!$room->isAvailable()) {
            return back()->withErrors(['room_id' => 'Phòng này đã có người thuê.'])->withInput();
        }

        $contract = Contract::create([
            'user_id'      => $request->user_id,
            'room_id'      => $request->room_id,
            'start_date'   => $request->start_date,
            'end_date'     => $request->end_date,
            'deposit'      => $request->deposit,
            'monthly_rent' => $request->monthly_rent,
            'notes'        => $request->notes,
            'status'       => 'active',
        ]);

        $room->update(['status' => 'rented']);

        return redirect()->route('admin.contracts.show', $contract)
            ->with('success', 'Tạo hợp đồng thành công! Phòng đã được đặt sang trạng thái đang thuê.');
    }

    public function show(Contract $contract)
    {
        if (auth()->user()->isLandlord() && $contract->room->landlord_id !== auth()->id()) {
            abort(403);
        }

        $contract->load(['user', 'room', 'invoices']);
        return view('admin.contracts.show', compact('contract'));
    }

    public function endContract(Contract $contract)
    {
        if (auth()->user()->isLandlord() && $contract->room->landlord_id !== auth()->id()) {
            abort(403);
        }

        $contract->update(['status' => 'ended']);
        $contract->room->update(['status' => 'available']);

        return back()->with('success', 'Hợp đồng đã kết thúc. Phòng đã được đặt về trạng thái trống.');
    }
}
