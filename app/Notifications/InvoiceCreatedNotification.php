<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(public Invoice $invoice) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('invoices.mail.created_subject', ['number' => $this->invoice->invoice_number]))
            ->greeting(__('invoices.mail.greeting'))
            ->line(__('invoices.mail.created_line', ['number' => $this->invoice->invoice_number]))
            ->line(__('invoices.mail.total_line', ['total' => number_format((float) $this->invoice->total, 2)]))
            ->action(
                __('invoices.mail.view_invoice_button'),
                route('invoices.show', $this->invoice)
            )
            ->line(__('invoices.mail.thanks'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'total' => (float) $this->invoice->total,
        ];
    }
}
