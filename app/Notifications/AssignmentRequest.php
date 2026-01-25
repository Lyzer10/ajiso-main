<?php

namespace App\Notifications;

use App\Models\Staff;
use App\Models\Dispute;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

use Illuminate\Notifications\Messages\MailMessage;

class AssignmentRequest extends Notification 
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
        $title = trim((string) optional($staff->user->designation)->name);
        $name = trim(implode(' ', array_filter([
            $staff->user->first_name ?? '',
            $staff->user->middle_name ?? '',
            $staff->user->last_name ?? '',
        ])));
        $this->full_name = $title !== '' && strtolower($title) !== 'other'
            ? trim($title.' '.$name)
            : $name;
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
                    ->level('info')
                    ->subject('OMBI LA KUBADILISHIWA SHAURI')
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
            'message' => $this->notification_message,
        ];
    }
}
