<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveRequestNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected LeaveRequest $leaveRequest,
        protected string $type = 'submitted'
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $employee = $this->leaveRequest->employee;
        $typeLabels = [
            'leave' => 'Cuti',
            'sick' => 'Sakit',
            'permission' => 'Izin',
        ];
        $typeLabel = $typeLabels[$this->leaveRequest->type] ?? 'Izin';

        if ($this->type === 'submitted') {
            return (new MailMessage)
                ->subject("Pengajuan {$typeLabel} dari {$employee->name}")
                ->greeting("Halo {$notifiable->name},")
                ->line("Karyawan **{$employee->name}** telah mengajukan {$typeLabel}.")
                ->line("**Tanggal:** {$this->leaveRequest->start_date} - {$this->leaveRequest->end_date}")
                ->line("**Alasan:** {$this->leaveRequest->reason}")
                ->action('Lihat Pengajuan', url('/leaves'))
                ->line('Terima kasih.');
        }

        if ($this->type === 'approved') {
            return (new MailMessage)
                ->subject("Pengajuan {$typeLabel} Disetujui")
                ->greeting("Halo {$employee->name},")
                ->line("Pengajuan {$typeLabel} Anda telah **disetujui**.")
                ->line("**Tanggal:** {$this->leaveRequest->start_date} - {$this->leaveRequest->end_date}")
                ->action('Lihat Riwayat', url('/leaves'))
                ->line('Terima kasih.');
        }

        return (new MailMessage)
            ->subject("Pengajuan {$typeLabel} Ditolak")
            ->greeting("Halo {$employee->name},")
            ->line("Pengajuan {$typeLabel} Anda telah **ditolak**.")
            ->line("**Tanggal:** {$this->leaveRequest->start_date} - {$this->leaveRequest->end_date}")
            ->action('Lihat Riwayat', url('/leaves'))
            ->line('Terima kasih.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'leave_request_id' => $this->leaveRequest->id,
            'type' => $this->type,
            'employee_name' => $this->leaveRequest->employee->name,
        ];
    }
}
