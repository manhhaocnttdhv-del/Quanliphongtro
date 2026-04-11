<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    /**
     * Danh sách hợp đồng của người thuê đang đăng nhập
     */
    public function index()
    {
        $user = auth()->user();

        $contracts = Contract::with(['room.images', 'invoices'])
            ->where('user_id', $user->id)
            ->orderByRaw("FIELD(status, 'active', 'ended')")
            ->latest()
            ->paginate(10);

        return view('user.contracts', compact('contracts'));
    }

    /**
     * Chi tiết 1 hợp đồng (chỉ chủ sở hữu mới xem được)
     */
    public function show(Contract $contract)
    {
        if ($contract->user_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền xem hợp đồng này.');
        }

        $contract->load(['room.images', 'invoices' => function ($q) {
            $q->latest();
        }]);

        return view('user.contract-detail', compact('contract'));
    }
}
