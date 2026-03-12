<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class LandlordController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isSuperAdmin()) {
                abort(403, 'Chỉ Super Admin mới có quyền truy cập.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $landlords = User::where('role', 'landlord')
            ->withCount('rooms')
            ->latest()
            ->paginate(15);

        return view('admin.landlords.index', compact('landlords'));
    }

    public function show(User $user)
    {
        if ($user->role !== 'landlord') abort(404);

        $user->load(['rooms' => function($q) {
            $q->withCount(['contracts', 'rentRequests']);
        }]);

        return view('admin.landlords.show', compact('user'));
    }
}
