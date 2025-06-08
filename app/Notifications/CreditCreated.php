<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Credit;

class CreditCreated extends Notification
{
    use Queueable;

    protected $credit;
    protected $isUpdate;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Credit $credit, $isUpdate = false)
    {
        $this->credit = $credit;
        $this->isUpdate = $isUpdate;
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
        $subject = $this->isUpdate 
            ? 'Crédito actualizado pendiente de aprobación' 
            : 'Nuevo crédito pendiente de aprobación';
            
        $message = $this->isUpdate
            ? "El crédito #{$this->credit->id} ha sido actualizado y requiere su aprobación."
            : "Hay un nuevo crédito #{$this->credit->id} que requiere su aprobación.";
            
        return (new MailMessage)
            ->subject($subject)
            ->greeting('Hola ' . $notifiable->name)
            ->line($message)
            ->line("Cliente: {$this->credit->user->name}")
            ->line("Monto: $" . number_format($this->credit->amount, 2))
            ->line("Interés: {$this->credit->utility}%")
            ->line("Monto Total: $" . number_format($this->credit->amount_neto, 2))
            ->line("Cuotas: {$this->credit->payment_number}")
            ->action('Revisar Solicitud', url("/credit/{$this->credit->id}/approve"))
            ->line('Gracias por utilizar nuestra aplicación.');
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
            'is_update' => $this->isUpdate,
            'message' => $this->isUpdate 
                ? "Crédito #{$this->credit->id} actualizado, pendiente de aprobación" 
                : "Nuevo crédito #{$this->credit->id} pendiente de aprobación"
        ];
    }
} 