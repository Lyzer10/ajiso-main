<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\Staff;
use App\Models\Beneficiary;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

use Illuminate\Notifications\Messages\MailMessage;

class CustomNotice extends Notification 
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($title, $priority, $message)
    {
        $this->notification_priority = $priority;
        $this->notification_title = $title;
        $this->notification_message = $message;
        
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail','database'];
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
                    ->level('info')
                    ->subject('TAARIFA KUTOKA MFUMO WA ALAS')
                    ->greeting('Habari!')
                    ->line($this->notification_message)
                    ->salutation('Ahsante.');
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
            'title' => $this->notification_title,
            'priority' => $this->notification_priority,
            'message' => $this->notification_message,
        ];
    }
}
