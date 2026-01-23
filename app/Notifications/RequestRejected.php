<?php

namespace App\Notifications;

use App\Models\Staff;
use App\Models\Dispute;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

use Illuminate\Notifications\Messages\MailMessage;

class RequestRejected extends Notification 
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Staff $staff, Dispute $dispute, $message)
    {
        $this->dispute_no = $dispute->dispute_no;
        $this->notification_email = $staff->user->email;
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
                    ->error()
                    ->subject('OMBI LA KUBADILISHIWA SHAURI LIMEKATALIWA')
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
            'Re(un)assignment request on dispute '.$this->dispute_no.' was rejected.',
        ];
    }
    //  testomg
}