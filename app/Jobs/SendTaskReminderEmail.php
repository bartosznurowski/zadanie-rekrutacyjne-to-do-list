<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Task;
use App\Mail\TaskReminderMail;
use Illuminate\Support\Facades\Mail;

class SendTaskReminderEmail implements ShouldQueue
{
    use Queueable;

    public $task;

    /**
     * Create a new job instance.
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = $this->task->user;

        if ($user && $user->email) {
            Mail::to($user->email)->send(new TaskReminderMail($this->task));
        }
    }
}
