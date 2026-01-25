<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\Dispute;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

use Illuminate\Notifications\Messages\MailMessage;

class RequestAccepted extends Notification 
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, Dispute $dispute, $message)
    {
        $this->dispute_no = $dispute->dispute_no;
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
                    ->success()
                    ->subject('OMBI LA KUBADILISHIWA SHAURI LIMEKUBALIWA')
                    ->greeting('Habari!')
                    ->line($this->notification_message)
                    ->salutation('Ahsante');
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
            'message' => 'Re(un)assignment request on dispute '.$this->dispute_no.' was accepted.',
        ];
    }
}
