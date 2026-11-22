<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('invoices:send-reminders')
    ->dailyAt('08:00')
    ->timezone('Asia/Kuala_Lumpur')
    ->withoutOverlapping()
    ->onOneServer();

Schedule::command('sanctum:prune-expired --hours=24')->daily();
Schedule::command('queue:prune-failed --hours=168')->daily();
