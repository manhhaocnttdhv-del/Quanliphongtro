<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminCommission;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            $routeName = $request->route()->getName();

            if ($routeName === 'admin.commissions.index') {
                if (!$user->isSuperAdmin() && !$user->isLandlord()) {
                    abort(403, 'Bạn không có quyền truy cập.');
                }
            } else {
                if (!$user->isSuperAdmin()) {
                    abort(403, 'Chỉ Super Admin mới có quyền thực hiện thao tác này.');
                }
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = AdminCommission::with(['landlord', 'invoice.room']);

        if ($user->isLandlord()) {
            $query->where('landlord_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $commissions = $query->latest()->paginate(15)->withQueryString();

        return view('admin.commissions.index', compact('commissions'));
    }

    public function markAsPaid(AdminCommission $commission)
    {
        $commission->update(['status' => 'paid']);
        return back()->with('success', 'Đã xác nhận thanh toán phí hoa hồng.');
    }
}
