<?php

namespace App\Notifications;

use App\Models\RentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewRentRequest extends Notification
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
            'title'   => 'Yêu cầu thuê phòng mới',
            'message' => $this->rentRequest->user->name . ' muốn thuê phòng ' . $this->rentRequest->room->name,
            'url'     => '/admin/rent-requests',
            'type'    => 'new_request',
        ];
    }
}
