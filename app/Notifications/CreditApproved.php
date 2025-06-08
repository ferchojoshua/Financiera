<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Credit;

class CreditApproved extends Notification
{
    use Queueable;

    protected $credit;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Credit $credit)
    {
        $this->credit = $credit;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Crédito Aprobado')
            ->greeting('Hola ' . $notifiable->name)
            ->line("El crédito #{$this->credit->id} ha sido aprobado.")
            ->line("Cliente: {$this->credit->user->name}")
            ->line("Monto: $" . number_format($this->credit->amount, 2))
            ->line("Monto Total: $" . number_format($this->credit->amount_neto, 2))
            ->line("Aprobado por: {$this->credit->approver->name}")
            ->when($this->credit->approval_notes, function ($message) {
                return $message->line("Notas: {$this->credit->approval_notes}");
            })
            ->action('Ver Detalles', url("/credit/{$this->credit->id}"))
            ->line('Ya puede proceder con el desembolso del crédito.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'credit_id' => $this->credit->id,
            'user_id' => $this->credit->user_id,
            'agent_id' => $this->credit->agent_id,
            'amount' => $this->credit->amount,
            'approved_by' => $this->credit->approved_by,
            'message' => "Crédito #{$this->credit->id} ha sido aprobado"
        ];
    }
} 