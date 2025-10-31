<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordChanged extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tu contraseña ha sido actualizada')
            ->greeting('Hola ' . ($notifiable->name ?? ''))
            ->line('Queremos informarte que tu contraseña fue cambiada correctamente.')
            ->line('Si no realizaste este cambio, por favor restablece tu contraseña de inmediato y contacta al soporte.')
            ->action('Ir al perfil', url('/perfil'))
            ->line('Este es un mensaje automático, por favor no respondas a este correo.');
    }
}
