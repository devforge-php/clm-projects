<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskUsersEmail extends Notification
{
    use Queueable;

    public $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('CLMGO.org — Yangi Task Tayinlandi ✅')
            ->greeting('Assalomu alaykum!')
            ->line('CLMGO.org platformasida siz uchun yangi task tayinlandi.')
            ->line('Task matni: "' . $this->task->text . '"')
            ->action('Taskni ko‘rish', url('https://clmgo.org/userTask'))
            ->line('Batafsil ko‘rish va topshiriqni bajarish uchun tugmani bosing.')
            ->salutation('CLM jamoasi bilan. Rahmat! 🤝');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'task_text' => $this->task->text,
        ];
    }
}
