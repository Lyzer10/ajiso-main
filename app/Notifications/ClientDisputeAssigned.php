<?php

namespace App\Notifications;

use App\Models\Dispute;
use App\Models\Beneficiary;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

use Illuminate\Notifications\Messages\MailMessage;

class ClientDisputeAssigned extends Notification 
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Beneficiary $beneficiary, Dispute $dispute, $message)
    {
        $this->full_name = $beneficiary->user->designation->designation.' '
                            .$beneficiary->user->first_name.' '
                            .$beneficiary->user->middle_name.' '
                            .$beneficiary->user->last_name;
        $this->dispute_no = $dispute->dispute_no;
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
                    ->level('info')
                    ->subject('KESI IMEPANGIWA MTOA MSAADA WA KISHERIA')
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
            'message' => 'Dispute with no. '.$this->dispute_no.' reported by beneficiary  '.$this->full_name.' was assigned to LAP.',
        ];
    }
}
