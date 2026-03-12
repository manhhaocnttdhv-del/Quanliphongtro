<?php

namespace App\Notifications;

use App\Models\RentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RentRequestApproved extends Notification
{
    use Queueable;

    public function __construct(public RentRequest $rentRequest) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'   => 'Yêu cầu thuê phòng đã được duyệt!',
            'message' => 'Yêu cầu thuê phòng ' . $this->rentRequest->room->name . ' của bạn đã được chấp thuận. Hợp đồng đã được tạo.',
            'url'     => '/my-invoices',
            'type'    => 'approved',
        ];
    }
}
