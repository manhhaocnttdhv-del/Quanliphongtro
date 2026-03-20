<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
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

    /**
     * Danh sách người dùng (tenant + landlord)
     */
    public function index(Request $request)
    {
        $query = User::query()->whereNotIn('role', ['super_admin']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->withCount(['rentRequests', 'contracts'])->latest()->paginate(15)->withQueryString();

        $totalTenants   = User::where('role', 'tenant')->count();
        $totalLandlords = User::where('role', 'landlord')->count();

        return view('admin.users.index', compact('users', 'totalTenants', 'totalLandlords'));
    }

    /**
     * Chi tiết người dùng
     */
    public function show(User $user)
    {
        if ($user->isSuperAdmin()) abort(404);

        $user->load([
            'rentRequests.room',
            'contracts.room',
        ]);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Đổi role người dùng (tenant ↔ landlord)
     */
    public function updateRole(Request $request, User $user)
    {
        if ($user->isSuperAdmin()) abort(403);

        $request->validate([
            'role' => 'required|in:landlord,tenant',
        ]);

        $user->update(['role' => $request->role]);

        return back()->with('success', 'Đã cập nhật vai trò: ' . ($request->role === 'landlord' ? 'Chủ trọ' : 'Người thuê'));
    }

    /**
     * Xoá tài khoản người dùng
     */
    public function destroy(User $user)
    {
        if ($user->isSuperAdmin() || $user->id === auth()->id()) {
            abort(403, 'Không thể xoá tài khoản này.');
        }

        // Kiểm tra có hợp đồng đang active không
        $activeContracts = $user->contracts()->where('status', 'active')->count();
        if ($activeContracts > 0) {
            return back()->with('error', 'Không thể xoá: người dùng đang có hợp đồng thuê phòng.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Đã xoá tài khoản người dùng.');
    }
}
