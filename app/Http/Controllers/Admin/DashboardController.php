<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Invoice;
use App\Models\Contract;
use App\Models\RentRequest;
use App\Models\User;
use App\Models\AdminCommission;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return $this->superAdminDashboard();
        } elseif ($user->isLandlord()) {
            return $this->landlordDashboard($user);
        }

        return redirect()->route('rooms.index');
    }

    private function superAdminDashboard()
    {
        $totalLandlords = User::where('role', 'landlord')->count();
        $totalRooms      = Room::count();
        $totalTenants    = User::where('role', 'tenant')->count();
        
        $totalRevenue = Invoice::where('status', 'paid')->sum('total_amount');
        $totalCommissions = AdminCommission::where('status', 'paid')->sum('amount');
        $pendingCommissions = AdminCommission::where('status', 'pending')->sum('amount');

        $recentLandlords = User::where('role', 'landlord')->latest()->take(5)->get();

        return view('admin.dashboard.super_admin', compact(
            'totalLandlords', 'totalRooms', 'totalTenants', 
            'totalRevenue', 'totalCommissions', 'pendingCommissions', 
            'chartData', 'recentLandlords'
        ));
    }

    private function landlordDashboard($user)
    {
        $totalRooms = Room::where('landlord_id', $user->id)->count();
        $rentedRooms = Room::where('landlord_id', $user->id)->where('status', 'rented')->count();
        $availableRooms = $totalRooms - $rentedRooms;
        
        $pendingRequests = RentRequest::whereHas('room', function($q) use ($user) {
            $q->where('landlord_id', $user->id);
        })->where('status', 'pending')->count();

        $monthlyIncome = Invoice::whereHas('room', function($q) use ($user) {
            $q->where('landlord_id', $user->id);
        })->where('status', 'paid')
          ->where('month', now()->month)
          ->where('year', now()->year)
          ->sum('total_amount');

        $pendingCommission = AdminCommission::where('landlord_id', $user->id)
            ->where('status', 'pending')
            ->sum('amount');

        $chartData = $this->getRevenueChartData($user->id);

        $recentInvoices = Invoice::whereHas('room', function($q) use ($user) {
            $q->where('landlord_id', $user->id);
        })->latest()->take(5)->get();

        return view('admin.dashboard.landlord', compact(
            'totalRooms', 'rentedRooms', 'availableRooms', 'pendingRequests',
            'monthlyIncome', 'pendingCommission', 'chartData', 'recentInvoices'
        ));
    }

    private function getRevenueChartData($landlordId = null)
    {
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $query = Invoice::where('status', 'paid')
                ->where('month', $date->month)
                ->where('year', $date->year);
            
            if ($landlordId) {
                $query->whereHas('room', function($q) use ($landlordId) {
                    $q->where('landlord_id', $landlordId);
                });
            }

            $revenue = $query->sum('total_amount');
            $chartData[] = [
                'label'   => $date->format('m/Y'),
                'revenue' => (float) $revenue,
            ];
        }
        return $chartData;
    }
}
