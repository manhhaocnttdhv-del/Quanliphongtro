<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Contract;
use App\Models\Utility;
use App\Models\Room;
use App\Notifications\InvoiceCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('manage-invoices');

        $user = auth()->user();
        $query = Invoice::with(['room', 'contract.user']);

        if ($user->isLandlord()) {
            $query->whereHas('room', function($q) use ($user) {
                $q->where('landlord_id', $user->id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        $invoices = $query->latest()->paginate(15)->withQueryString();

        return view('admin.invoices.index', compact('invoices'));
    }

    public function create()
    {
        Gate::authorize('manage-invoices');

        $user = auth()->user();
        $activeContracts = Contract::where('status', 'active')
            ->whereHas('room', function($q) use ($user) {
                if ($user->isLandlord()) {
                    $q->where('landlord_id', $user->id);
                }
            })
            ->with(['user', 'room'])->get();
            
        $month = now()->month;
        $year  = now()->year;

        return view('admin.invoices.create', compact('activeContracts', 'month', 'year'));
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-invoices');

        $request->validate([
            'contract_id'     => 'required|exists:contracts,id',
            'month'           => 'required|integer|between:1,12',
            'year'            => 'required|integer|min:2020',
            'room_fee'        => 'required|numeric|min:0',
            'electricity_fee' => 'required|numeric|min:0',
            'water_fee'       => 'required|numeric|min:0',
            'service_fee'     => 'required|numeric|min:0',
            'due_date'        => 'nullable|date',
            'notes'           => 'nullable|string',
        ]);

        $contract = Contract::findOrFail($request->contract_id);
        
        if (auth()->user()->isLandlord() && $contract->room->landlord_id !== auth()->id()) {
            abort(403);
        }

        $totalAmount = $request->room_fee + $request->electricity_fee + $request->water_fee + $request->service_fee;

        $invoice = Invoice::create([
            'room_id'         => $contract->room_id,
            'contract_id'     => $contract->id,
            'month'           => $request->month,
            'year'            => $request->year,
            'room_fee'        => $request->room_fee,
            'electricity_fee' => $request->electricity_fee,
            'water_fee'       => $request->water_fee,
            'service_fee'     => $request->service_fee,
            'total_amount'    => $totalAmount,
            'status'          => Invoice::STATUS_UNPAID,
            'transaction_id'  => 'INV-' . strtoupper(Str::random(8)),
            'due_date'        => $request->due_date ?? now()->addDays(15),
            'notes'           => $request->notes,
        ]);

        // Notify tenant
        $contract->user->notify(new InvoiceCreated($invoice));

        return redirect()->route('admin.invoices.index')
            ->with('success', 'Tạo hóa đơn thành công!');
    }

    public function show(Invoice $invoice)
    {
        Gate::authorize('manage-invoices');

        if (auth()->user()->isLandlord() && $invoice->room->landlord_id !== auth()->id()) {
            abort(403);
        }

        $invoice->load(['room', 'contract.user']);
        return view('admin.invoices.show', compact('invoice'));
    }

    public function confirmPayment(Request $request, Invoice $invoice)
    {
        Gate::authorize('manage-invoices');

        if (auth()->user()->isLandlord() && $invoice->room->landlord_id !== auth()->id()) {
            abort(403);
        }

        // Chống trùng thanh toán
        if ($invoice->isPaid()) {
            return back()->with('error', 'Hóa đơn đã được thanh toán trước đó!');
        }

        $request->validate([
            'payment_method' => 'required|string',
            'payment_ref'    => 'nullable|string|max:255',
        ]);

        $invoice->update([
            'status'         => Invoice::STATUS_PAID,
            'payment_method' => $request->payment_method,
            'payment_ref'    => $request->payment_ref,
            'paid_at'        => now(),
        ]);

        // Ghi nhận hoa hồng
        $rate = 5;
        $commissionAmount = ($invoice->total_amount * $rate) / 100;

        \App\Models\AdminCommission::create([
            'landlord_id' => $invoice->room->landlord_id,
            'invoice_id'  => $invoice->id,
            'amount'      => $commissionAmount,
            'rate'        => $rate,
            'status'      => 'pending',
        ]);

        return back()->with('success', 'Đã xác nhận thanh toán và ghi nhận phí hoa hồng!');
    }

    /**
     * Hủy hóa đơn
     */
    public function cancel(Invoice $invoice)
    {
        Gate::authorize('manage-invoices');

        if ($invoice->isPaid()) {
            return back()->with('error', 'Không thể hủy hóa đơn đã thanh toán!');
        }

        $invoice->update([
            'status' => Invoice::STATUS_CANCELLED,
        ]);

        return back()->with('success', 'Đã hủy hóa đơn!');
    }

    public function getUtilityData(Request $request)
    {
        $utility = Utility::where('room_id', $request->room_id)
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->first();

        return response()->json($utility);
    }
}
