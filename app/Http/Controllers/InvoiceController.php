<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Get invoices via user's active contracts
        $invoices = Invoice::whereHas('contract', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with(['room', 'contract'])
            ->latest()
            ->paginate(10);

        return view('user.invoices', compact('invoices'));
    }

    public function show(Invoice $invoice)
    {
        // Ensure user owns this invoice
        if ($invoice->contract && $invoice->contract->user_id !== auth()->id()) {
            abort(403);
        }

        $invoice->load('room', 'contract');

        return view('user.invoice-detail', compact('invoice'));
    }
}
