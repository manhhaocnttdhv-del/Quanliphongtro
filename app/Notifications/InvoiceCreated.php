<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InvoiceCreated extends Notification
{
    use Queueable;

    public function __construct(public Invoice $invoice) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'   => 'Hóa đơn tháng ' . $this->invoice->month . '/' . $this->invoice->year . ' đã được tạo',
            'message' => 'Hóa đơn phòng ' . $this->invoice->room->name . ' tháng ' . $this->invoice->month . '/' . $this->invoice->year . ': ' . number_format($this->invoice->total_amount) . ' VNĐ',
            'url'     => '/my-invoices',
            'type'    => 'invoice',
        ];
    }
}
