<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserCreated extends Notification 
{
    use Queueable;

    public $full_name;
    public $notification_email;
    public $notification_message;
    public $plain_password;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, $plain_password = null)
    {
        $this->full_name = $user->designation->name.' '
                            .$user->first_name.' '
                            .$user->middle_name.' '
                            .$user->last_name;
        $this->notification_email = $user->email;
        $this->plain_password = $plain_password;
        $passwordText = $this->plain_password ? $this->plain_password : 'ALAS%2021';
        $this->notification_message = "Hongera akaunti yako imewezeshwa.
                            Tafadhali tumia barua pepe yako na Nywila hii
                            '{$passwordText}' ili kuingia kwenye mfumo.
                            ";
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
                    ->action('Ingia', 'https://ajisolegalaid.org/en/login')
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
            'message' => 'User account for '.$this->full_name.' was created.',
        ];
    }
}
