<?php

namespace App\Console\Commands;

use App\Models\Contract;
use App\Notifications\ContractExpiring;
use Illuminate\Console\Command;

class CheckExpiringContracts extends Command
{
    protected $signature   = 'contracts:check-expiring';
    protected $description = 'Gửi thông báo cho chủ trọ về các hợp đồng sắp hết hạn trong 30 ngày';

    public function handle(): void
    {
        $contracts = Contract::with(['room.landlord', 'user'])
            ->where('status', 'active')
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [now(), now()->addDays(30)])
            ->get();

        $count = 0;
        foreach ($contracts as $item) {
            /** @var Contract $item */
            $landlord = $item->room->landlord ?? null;
            if ($landlord) {
                $landlord->notify(new ContractExpiring($item));
                $count++;
            }
        }

        $this->info("Đã gửi {$count} thông báo hợp đồng sắp hết hạn.");
    }
}
