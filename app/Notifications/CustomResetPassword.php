<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomResetPassword extends Notification
{
    use Queueable;

    protected $token;
    protected $name;

    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $resetLink = url(route('password.reset', $this->token, false));
        $expiration = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire');
    
        return (new MailMessage)
            ->subject('Reset Password - PT Anagata Sisedu Nusantara')
            ->view('emails.reset_password', [
                'name' => $notifiable->name,
                'resetLink' => $resetLink,
                'expiration' => $expiration
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}