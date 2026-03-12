<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RentRequest;
use App\Models\Contract;
use App\Notifications\RentRequestApproved;
use App\Notifications\RentRequestRejected;
use Illuminate\Http\Request;

class RentRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = RentRequest::with(['user', 'room'])->latest();

        if ($user->isLandlord()) {
            $query->whereHas('room', function($q) use ($user) {
                $q->where('landlord_id', $user->id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $rentRequests = $query->paginate(15)->withQueryString();

        return view('admin.rent-requests.index', compact('rentRequests'));
    }

    public function approve(Request $request, RentRequest $rentRequest)
    {
        // Authorization check
        if (auth()->user()->isLandlord() && $rentRequest->room->landlord_id !== auth()->id()) {
            abort(403);
        }

        if ($rentRequest->status !== 'pending') {
            return back()->with('error', 'Yêu cầu này đã được xử lý.');
        }

        $request->validate([
            'start_date'    => 'required|date',
            'end_date'      => 'nullable|date|after:start_date',
            'deposit'       => 'required|numeric|min:0',
            'notes'         => 'nullable|string',
        ]);

        // Create contract
        $contract = Contract::create([
            'user_id'         => $rentRequest->user_id,
            'room_id'         => $rentRequest->room_id,
            'rent_request_id' => $rentRequest->id,
            'start_date'      => $request->start_date,
            'end_date'        => $request->end_date,
            'deposit'         => $request->deposit,
            'monthly_rent'    => $rentRequest->room->price,
            'notes'           => $request->notes,
            'status'          => 'active',
        ]);

        // Update room status
        $rentRequest->room->update(['status' => 'rented']);

        // Update rent request status
        $rentRequest->update(['status' => 'approved']);

        // Notify user
        $rentRequest->user->notify(new RentRequestApproved($rentRequest));

        return redirect()->route('admin.rent-requests.index')
            ->with('success', 'Đã duyệt yêu cầu và tạo hợp đồng!');
    }

    public function reject(RentRequest $rentRequest)
    {
        // Authorization check
        if (auth()->user()->isLandlord() && $rentRequest->room->landlord_id !== auth()->id()) {
            abort(403);
        }

        if ($rentRequest->status !== 'pending') {
            return back()->with('error', 'Yêu cầu này đã được xử lý.');
        }

        $rentRequest->update(['status' => 'rejected']);

        // Notify user
        $rentRequest->user->notify(new RentRequestRejected($rentRequest));

        return back()->with('success', 'Đã từ chối yêu cầu thuê phòng.');
    }
}
