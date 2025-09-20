<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MentionedInComment extends Notification
{
    use Queueable;

    protected $comment;

    public function __construct($comment)
    {
        $this->comment = $comment;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('You were mentioned in a comment.')
                    ->action('View', url('/tasks/' . $this->comment->task_id . '#comments'))
                    ->line('Thank you!');
    }

    public function toArray($notifiable)
    {
        return [
            'comment_id' => $this->comment->id,
            'task_id' => $this->comment->task_id,
            'type' => 'mentioned',
        ];
    }
}
