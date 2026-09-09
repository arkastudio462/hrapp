<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionExpiringNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Tenant $tenant,
        protected int $daysRemaining
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Trial Anda Berakhir dalam {$this->daysRemaining} Hari")
            ->greeting("Halo {$notifiable->name},")
            ->line("Trial Anda untuk **{$this->tenant->name}** akan berakhir dalam **{$this->daysRemaining} hari**.")
            ->line('Untuk melanjutkan menggunakan layanan kami, silakan upgrade ke paket berbayar.')
            ->action('Upgrade Sekarang', url('/subscription/packages'))
            ->line('Terima kasih.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'tenant_id' => $this->tenant->id,
            'days_remaining' => $this->daysRemaining,
        ];
    }
}
