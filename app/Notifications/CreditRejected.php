<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Credit;

class CreditRejected extends Notification
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
        $mailMessage = (new MailMessage)
            ->subject('Crédito Rechazado')
            ->greeting('Hola ' . $notifiable->name)
            ->line("El crédito #{$this->credit->id} ha sido rechazado.")
            ->line("Cliente: {$this->credit->user->name}")
            ->line("Monto: $" . number_format($this->credit->amount, 2))
            ->line("Rechazado por: {$this->credit->approver->name}");
            
        if ($this->credit->approval_notes) {
            $mailMessage->line("Motivo del rechazo: {$this->credit->approval_notes}");
        }
        
        return $mailMessage
            ->action('Ver Detalles', url("/credit/{$this->credit->id}"))
            ->line('Por favor, revise las observaciones y realice los ajustes necesarios si desea volver a solicitar el crédito.');
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
            'rejected_by' => $this->credit->approved_by,
            'rejection_notes' => $this->credit->approval_notes,
            'message' => "Crédito #{$this->credit->id} ha sido rechazado"
        ];
    }
} 