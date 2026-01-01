<?php

namespace App\Notifications;

use App\Models\Beneficiary;
use Illuminate\Bus\Queueable;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BeneficiaryEnrolled extends Notification 
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Beneficiary $beneficiary, $message)
    {
        $this->full_name = $beneficiary->user->designation->designation.' '
                            .$beneficiary->user->first_name.' '
                            .$beneficiary->user->middle_name.' '
                            .$beneficiary->user->last_name;
        $this->notification_email = $beneficiary->user->email;
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
                    ->subject('USAJILI UMEKAMILIKA')
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
            'message' => 'Beneficiary account for '.$this->full_name.' was created.',
        ];
    }
}