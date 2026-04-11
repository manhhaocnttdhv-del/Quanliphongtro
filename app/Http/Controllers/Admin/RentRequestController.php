<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RentRequest;
use App\Models\Contract;
use App\Models\User;
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
            $query->whereHas('room', function ($q) use ($user) {
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
            'start_date'   => 'required|date',
            'end_date'     => 'nullable|date|after:start_date',
            'deposit'      => 'required|numeric|min:0',
            'monthly_rent' => 'required|numeric|min:0',
            'notes'        => 'nullable|string',
        ]);

        // Create contract với giá thuê có thể đã thương lượng
        $contract = Contract::create([
            'user_id'         => $rentRequest->user_id,
            'room_id'         => $rentRequest->room_id,
            'rent_request_id' => $rentRequest->id,
            'start_date'      => $request->start_date,
            'end_date'        => $request->end_date,
            'deposit'         => $request->deposit,
            'monthly_rent'    => $request->monthly_rent,
            'notes'           => $request->notes,
            'status'          => 'active',
        ]);

        // Lưu giá đã thương lượng vào request
        $rentRequest->update([
            'status'      => 'approved',
            'agreed_rent' => $request->monthly_rent,
        ]);

        // Update room status
        $rentRequest->room->update(['status' => 'rented']);

        // ── AUTO-REJECT tất cả pending request khác của cùng phòng ──
        $otherPending = RentRequest::where('room_id', $rentRequest->room_id)
            ->where('id', '!=', $rentRequest->id)
            ->where('status', 'pending')
            ->get();

        foreach ($otherPending as $other) {
            $other->update(['status' => 'rejected']);
            // Notify những người bị reject để họ biết phòng đã có người
            try {
                $other->user->notify(new RentRequestRejected($other));
            } catch (\Exception $e) {
                // Không để lỗi notify chặn flow chính
            }
        }

        // Notify người được duyệt
        $rentRequest->user->notify(new RentRequestApproved($rentRequest));

        return redirect()->route('admin.rent-requests.index')
            ->with('success', 'Đã duyệt yêu cầu và tạo hợp đồng!' .
                ($otherPending->count() > 0 ? " ({$otherPending->count()} yêu cầu khác đã tự động từ chối)" : ''));
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
