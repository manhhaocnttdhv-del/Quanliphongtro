<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        //
    ];

    public function boot(): void
    {
        // ─── Super Admin: toàn quyền ──────────────────────────────
        Gate::define('manage-system', fn ($user) => $user->isSuperAdmin());
        Gate::define('manage-users', fn ($user) => $user->isSuperAdmin());
        Gate::define('manage-commissions', fn ($user) => $user->isSuperAdmin() || $user->isLandlord());
        Gate::define('manage-settings', fn ($user) => $user->isSuperAdmin() || $user->isLandlord());

        // ─── Staff + Super Admin: duyệt nội dung ─────────────────
        Gate::define('approve-rooms', fn ($user) => $user->isSuperAdmin() || $user->isStaff());
        Gate::define('moderate-content', fn ($user) => $user->isSuperAdmin() || $user->isStaff());
        Gate::define('view-reports', fn ($user) => $user->isSuperAdmin() || $user->isStaff() || $user->isLandlord());

        // ─── Landlord: quản lý phòng trọ ─────────────────────────
        Gate::define('manage-rooms', fn ($user) => $user->isLandlord());
        Gate::define('manage-contracts', fn ($user) => $user->isLandlord());
        Gate::define('manage-invoices', fn ($user) => $user->isLandlord());
        Gate::define('manage-utilities', fn ($user) => $user->isLandlord());
        Gate::define('manage-rent-requests', fn ($user) => $user->isLandlord() || $user->isStaff() || $user->isSuperAdmin());
        Gate::define('manage-maintenance', fn ($user) => $user->isLandlord() || $user->isStaff() || $user->isSuperAdmin());

        // ─── Tenant: thuê phòng ──────────────────────────────────
        Gate::define('rent-rooms', fn ($user) => $user->isTenant());
        Gate::define('view-own-invoices', fn ($user) => $user->isTenant());

        // ─── Access admin panel ──────────────────────────────────
        Gate::define('access-admin', fn ($user) => $user->isAdmin());
    }
}
