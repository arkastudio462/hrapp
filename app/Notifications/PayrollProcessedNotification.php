<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Payroll;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayrollProcessedNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Payroll $payroll
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $employee = $this->payroll->employee;
        $period = $this->payroll->period;

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $monthName = $months[$period->month] ?? '';
        $formattedSalary = 'Rp '.number_format($this->payroll->net_salary, 0, ',', '.');

        return (new MailMessage)
            ->subject("Payslip {$monthName} {$period->year}")
            ->greeting("Halo {$employee->name},")
            ->line("Payslip untuk periode **{$monthName} {$period->year}** telah tersedia.")
            ->line("**Gaji Bersih:** {$formattedSalary}")
            ->action('Lihat Payslip', url("/payroll/payslip/{$this->payroll->id}"))
            ->line('Terima kasih.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'payroll_id' => $this->payroll->id,
            'period_month' => $this->payroll->period->month,
            'period_year' => $this->payroll->period->year,
            'net_salary' => $this->payroll->net_salary,
        ];
    }
}
