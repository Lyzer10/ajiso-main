<?php

namespace App\Notifications;

use App\Models\Dispute;
use App\Models\Staff;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class StaffDisputeAssigned extends Notification
{
    use Queueable;

    public $full_name;
    public $dispute_no;
    public $notification_email;
    public $notification_message;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Staff $staff, Dispute $dispute, $message)
    {
        $this->full_name = $staff->user->designation->designation . ' '
            . $staff->user->first_name . ' '
            . $staff->user->middle_name . ' '
            . $staff->user->last_name;
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
        return ['database', 'mail'];
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
            ->subject('KUPANGIWA KESI')
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
            'message' => 'Dispute with no. ' . $this->dispute_no . ' was assigned to ' . $this->full_name,
        ];
    }
}
