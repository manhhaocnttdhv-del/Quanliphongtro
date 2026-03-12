<?php

namespace App\Notifications;

use App\Models\RentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RentRequestRejected extends Notification
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
            'title'   => 'Yêu cầu thuê phòng bị từ chối',
            'message' => 'Yêu cầu thuê phòng ' . $this->rentRequest->room->name . ' của bạn đã bị từ chối. Vui lòng liên hệ chủ nhà để biết thêm thông tin.',
            'url'     => '/rooms',
            'type'    => 'rejected',
        ];
    }
}
