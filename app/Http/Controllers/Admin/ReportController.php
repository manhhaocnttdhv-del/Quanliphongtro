<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\User;
use App\Notifications\InvoiceCreated;
use App\Notifications\ContractExpiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Revenue by month (last 12 months)
        $revenueChart = [];
        for ($i = 11; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $query = Invoice::where('status', 'paid')
                ->where('month', $date->month)
                ->where('year', $date->year);

            if ($user->isLandlord()) {
                $query->whereHas('room', fn($q) => $q->where('landlord_id', $user->id));
            }

            $revenueChart[] = [
                'label'   => $date->format('m/Y'),
                'revenue' => (float) $query->sum('total_amount'),
            ];
        }

        // Room occupancy
        $roomQuery = Room::query();
        if ($user->isLandlord()) {
            $roomQuery->where('landlord_id', $user->id);
        }
        $totalRooms     = $roomQuery->count();
        $rentedRooms    = $roomQuery->where('status', 'rented')->count();
        $availableRooms = $totalRooms - $rentedRooms;
        $occupancyRate  = $totalRooms > 0 ? round(($rentedRooms / $totalRooms) * 100, 1) : 0;

        // Pending invoices (debt)
        $debtQuery = Invoice::with(['room', 'contract.user'])
            ->where('status', 'unpaid');
        if ($user->isLandlord()) {
            $debtQuery->whereHas('room', fn($q) => $q->where('landlord_id', $user->id));
        }
        $pendingInvoices  = $debtQuery->latest()->get();
        $totalDebtAmount  = $pendingInvoices->sum('total_amount');

        // Contracts expiring in 30 days
        $expiringQuery = Contract::with(['user', 'room'])
            ->where('status', 'active')
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [now(), now()->addDays(30)]);
        if ($user->isLandlord()) {
            $expiringQuery->whereHas('room', fn($q) => $q->where('landlord_id', $user->id));
        }
        $expiringContracts = $expiringQuery->orderBy('end_date')->get();

        // Revenue total this month & year
        $monthRevQuery = Invoice::where('status', 'paid')
            ->where('month', now()->month)
            ->where('year', now()->year);
        $yearRevQuery = Invoice::where('status', 'paid')
            ->where('year', now()->year);
        if ($user->isLandlord()) {
            $monthRevQuery->whereHas('room', fn($q) => $q->where('landlord_id', $user->id));
            $yearRevQuery->whereHas('room', fn($q) => $q->where('landlord_id', $user->id));
        }
        $monthRevenue = $monthRevQuery->sum('total_amount');
        $yearRevenue  = $yearRevQuery->sum('total_amount');

        return view('admin.reports.index', compact(
            'revenueChart', 'totalRooms', 'rentedRooms', 'availableRooms',
            'occupancyRate', 'pendingInvoices', 'totalDebtAmount',
            'expiringContracts', 'monthRevenue', 'yearRevenue'
        ));
    }
}
