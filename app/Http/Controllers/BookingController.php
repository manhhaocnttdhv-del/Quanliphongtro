<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Contract;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BookingController extends Controller
{
    // ─── Bước 2: Form nhập thông tin đặt phòng ──────────
    public function create(Room $room)
    {
        // Kiểm tra phòng còn trống không
        if (!$room->isAvailable()) {
            return redirect()->route('rooms.show', $room)
                ->with('error', 'Phòng này hiện không còn trống hoặc đã được đặt.');
        }

        // Kiểm tra user đã có booking pending chưa
        $existingBooking = Booking::where('user_id', auth()->id())
            ->where('room_id', $room->id)
            ->where('status', 'pending')
            ->where('expired_at', '>', now())
            ->first();

        if ($existingBooking) {
            return redirect()->route('bookings.payment', $existingBooking)
                ->with('info', 'Bạn đã có đơn đặt phòng này đang chờ thanh toán.');
        }

        // Tính deposit mặc định = 1 tháng tiền thuê
        $defaultDeposit = $room->price;

        return view('bookings.create', compact('room', 'defaultDeposit'));
    }

    // ─── Bước 4: Tạo booking ─────────────────────────────
    public function store(Request $request, Room $room)
    {
        if (!$room->isAvailable()) {
            return redirect()->route('rooms.show', $room)
                ->with('error', 'Phòng đã được đặt bởi người khác. Vui lòng chọn phòng khác.');
        }

        $validated = $request->validate([
            'tenant_name'    => ['required', 'string', 'max:100'],
            'tenant_phone'   => ['required', 'string', 'max:20', 'regex:/^[0-9\s\+\-]{9,15}$/'],
            'tenant_cccd'    => ['nullable', 'string', 'max:20'],
            'num_people'     => ['required', 'integer', 'min:1', 'max:20'],
            'move_in_date'   => ['nullable', 'date', 'after_or_equal:today'],
            'note'           => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'in:offline,online'],
            'deposit_amount' => ['required', 'numeric', 'min:0'],
        ]);

        $booking = Booking::create([
            'room_id'        => $room->id,
            'user_id'        => auth()->id(),
            'tenant_name'    => $validated['tenant_name'],
            'tenant_phone'   => $validated['tenant_phone'],
            'tenant_cccd'    => $validated['tenant_cccd'],
            'num_people'     => $validated['num_people'],
            'move_in_date'   => $validated['move_in_date'],
            'note'           => $validated['note'],
            'deposit_amount' => $validated['deposit_amount'],
            'payment_method' => $validated['payment_method'],
            'status'         => 'pending',
            'expired_at'     => Carbon::now()->addHours(24),
        ]);

        // Nếu thanh toán online → redirect sang cổng thanh toán (tạm thời giả lập)
        if ($validated['payment_method'] === 'online') {
            return redirect()->route('bookings.payment', $booking);
        }

        // Offline → trang xác nhận chờ admin
        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Đặt phòng thành công! Vui lòng đến văn phòng thanh toán tiền cọc để xác nhận.');
    }

    // ─── Trang chi tiết booking của user ─────────────────
    public function show(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        $booking->load(['room.images', 'contract']);
        return view('bookings.show', compact('booking'));
    }

    // ─── Trang thanh toán online ──────────────────────────
    public function payment(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$booking->isPending()) {
            return redirect()->route('bookings.show', $booking);
        }

        $booking->load(['room.images']);
        return view('bookings.payment', compact('booking'));
    }

    // ─── Giả lập callback thanh toán online thành công ───
    public function paymentSuccess(Request $request, Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$booking->isPending()) {
            return redirect()->route('bookings.show', $booking);
        }

        // Trong thực tế: verify callback từ VNPay/MoMo
        $booking->update([
            'status'      => 'paid',
            'paid_at'     => now(),
            'payment_ref' => 'ONLINE-' . strtoupper(uniqid()),
        ]);

        // Cập nhật trạng thái phòng → reserved (đang được giữ chỗ)
        $booking->room->update(['status' => 'reserved']);

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Thanh toán tiền cọc thành công! Phòng đã được đặt cho bạn.');
    }

    // ─── Danh sách bookings của user ─────────────────────
    public function myBookings()
    {
        $bookings = Booking::where('user_id', auth()->id())
            ->with(['room.images'])
            ->latest()
            ->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    // ─── Hủy booking (user tự hủy) ───────────────────────
    public function cancel(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$booking->isPending()) {
            return back()->with('error', 'Không thể hủy đơn đặt này.');
        }

        $booking->update(['status' => 'cancelled']);

        return redirect()->route('bookings.my')
            ->with('success', 'Đã hủy đơn đặt phòng.');
    }
}
