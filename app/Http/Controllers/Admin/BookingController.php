<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Contract;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    // ─── Danh sách tất cả bookings ───────────────────────
    public function index(Request $request)
    {
        $user  = auth()->user();
        $query = Booking::with(['room', 'user', 'confirmedBy']);

        // Landlord chỉ thấy bookings của phòng mình
        if ($user->isLandlord()) {
            $query->whereHas('room', fn($q) => $q->where('landlord_id', $user->id));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Tự động expire các booking quá hạn
        Booking::where('status', 'pending')
            ->where('expired_at', '<', now())
            ->update(['status' => 'cancelled']);

        $bookings = $query->latest()->paginate(15)->withQueryString();
        $stats = [
            'pending'   => Booking::when($user->isLandlord(), fn($q) => $q->whereHas('room', fn($r) => $r->where('landlord_id', $user->id)))->where('status', 'pending')->count(),
            'paid'      => Booking::when($user->isLandlord(), fn($q) => $q->whereHas('room', fn($r) => $r->where('landlord_id', $user->id)))->where('status', 'paid')->count(),
            'cancelled' => Booking::when($user->isLandlord(), fn($q) => $q->whereHas('room', fn($r) => $r->where('landlord_id', $user->id)))->where('status', 'cancelled')->count(),
            'converted' => Booking::when($user->isLandlord(), fn($q) => $q->whereHas('room', fn($r) => $r->where('landlord_id', $user->id)))->where('status', 'converted')->count(),
        ];

        return view('admin.bookings.index', compact('bookings', 'stats'));
    }

    // ─── Chi tiết booking ─────────────────────────────────
    public function show(Booking $booking)
    {
        $user = auth()->user();
        if ($user->isLandlord() && $booking->room->landlord_id !== $user->id) {
            abort(403);
        }

        $booking->load(['room.images', 'user', 'confirmedBy', 'contract']);
        return view('admin.bookings.show', compact('booking'));
    }

    // ─── Xác nhận thanh toán offline ─────────────────────
    public function confirmPayment(Booking $booking)
    {
        $user = auth()->user();
        if ($user->isLandlord() && $booking->room->landlord_id !== $user->id) {
            abort(403);
        }

        if (!$booking->isPending()) {
            return back()->with('error', 'Booking này không ở trạng thái chờ thanh toán.');
        }

        $booking->update([
            'status'       => 'paid',
            'paid_at'      => now(),
            'confirmed_by' => auth()->id(),
            'payment_ref'  => 'OFFLINE-' . strtoupper(uniqid()),
        ]);

        // Cập nhật room → reserved
        $booking->room->update(['status' => 'reserved']);

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', 'Đã xác nhận thanh toán cọc. Phòng chuyển sang trạng thái đã đặt.');
    }

    // ─── Hủy booking (admin) ─────────────────────────────
    public function cancel(Booking $booking)
    {
        $user = auth()->user();
        if ($user->isLandlord() && $booking->room->landlord_id !== $user->id) {
            abort(403);
        }

        if ($booking->status === 'converted') {
            return back()->with('error', 'Không thể hủy booking đã tạo hợp đồng.');
        }

        $oldStatus = $booking->status;
        $booking->update(['status' => 'cancelled']);

        // Nếu đã paid → trả lại trạng thái phòng available
        if ($oldStatus === 'paid') {
            $booking->room->update(['status' => 'available']);
        }

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', 'Đã hủy booking.');
    }

    // ─── Bước 7: Chuyển booking → hợp đồng ──────────────
    public function convertToContract(Request $request, Booking $booking)
    {
        $user = auth()->user();
        if ($user->isLandlord() && $booking->room->landlord_id !== $user->id) {
            abort(403);
        }

        if (!$booking->isPaid()) {
            return back()->with('error', 'Chỉ có thể tạo hợp đồng từ booking đã thanh toán cọc.');
        }

        if ($booking->contract_id) {
            return redirect()->route('admin.contracts.show', $booking->contract)
                ->with('info', 'Hợp đồng đã được tạo trước đó.');
        }

        $validated = $request->validate([
            'start_date'   => ['required', 'date'],
            'end_date'     => ['nullable', 'date', 'after:start_date'],
            'monthly_rent' => ['required', 'numeric', 'min:0'],
            'notes'        => ['nullable', 'string'],
        ]);

        $contract = Contract::create([
            'user_id'      => $booking->user_id,
            'room_id'      => $booking->room_id,
            'start_date'   => $validated['start_date'],
            'end_date'     => $validated['end_date'],
            'deposit'      => $booking->deposit_amount,
            'monthly_rent' => $validated['monthly_rent'],
            'notes'        => $validated['notes'],
            'status'       => 'active',
        ]);

        // Cập nhật booking → converted & gắn contract
        $booking->update([
            'status'      => 'converted',
            'contract_id' => $contract->id,
        ]);

        // Cập nhật phòng → rented
        $booking->room->update(['status' => 'rented']);

        return redirect()->route('admin.contracts.show', $contract)
            ->with('success', 'Hợp đồng đã được tạo thành công từ đơn đặt phòng!');
    }
}
