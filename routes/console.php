<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Task;
use App\Jobs\SendTaskReminderEmail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    $tasks = Task::whereDate('due_date', Carbon::tomorrow())
        ->whereIn('status', ['to-do', 'in progress'])
        ->with('user')
        ->get();

    foreach($tasks as $task) {
        dispatch(new SendTaskReminderEmail($task));
    }
})->dailyAt('08:00');
