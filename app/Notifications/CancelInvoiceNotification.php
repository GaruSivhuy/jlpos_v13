<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

class CancelInvoiceNotification extends Notification
{
    public function __construct(
        public Invoice $invoice,
        public ?string $cancelledBy,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [TelegramChannel::class];
    }

    public function toTelegram(object $notifiable): TelegramMessage
    {
        return TelegramMessage::create()
            ->content(implode("\n", [
                ' Cancel Invoice Notification ',
                '- Date : '.$this->invoice->payment_date?->format('Y-m-d'),
                '- Invoice Code : '.$this->invoice->invoice_code,
                '- Amount: '.$this->invoice->total.' $',
                '- Payment Type : '.$this->invoice->paymentGateway?->name,
                '- Cancel Invoice By : '.$this->cancelledBy,
            ]));
    }
}
