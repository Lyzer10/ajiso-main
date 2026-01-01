<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

use Illuminate\Notifications\Messages\MailMessage;

class SendClientNotification extends Notification 
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user = null, $message)
    {
        $this->full_name = $user->designation->designation.' '
                            .$user->first_name.' '
                            .$user->middle_name.' '
                            .$user->last_name;
        $this->notification_email = $user->email;

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
        if (!is_null($this->notification_email) && !empty($this->notification_email)) {
            return ['mail','database'];
        } else {
            return ['database'];
        }
        
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
                    ->subject('UJUMBE KUTOKA KWA MTOA MSAADA WA KISHERIA AJISO')
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
            'message' => 'SMS and Email Notification sent to '.$this->full_name.'.',
        ];
    }
}
