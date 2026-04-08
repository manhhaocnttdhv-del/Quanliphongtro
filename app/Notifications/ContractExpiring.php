<?php

namespace App\Notifications;

use App\Models\Contract;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ContractExpiring extends Notification
{
    use Queueable;

    public function __construct(public Contract $contract) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $daysLeft = now()->diffInDays($this->contract->end_date, false);
        $roomName = $this->contract->room->name ?? 'Không xác định';
        $tenantName = $this->contract->user->name ?? 'Không xác định';

        return [
            'title'       => "⏰ Hợp đồng phòng {$roomName} sắp hết hạn",
            'message'     => "Khách thuê {$tenantName} còn {$daysLeft} ngày. Hợp đồng kết thúc: {$this->contract->end_date->format('d/m/Y')}.",
            'contract_id' => $this->contract->id,
            'room_name'   => $roomName,
            'days_left'   => $daysLeft,
        ];
    }
}
