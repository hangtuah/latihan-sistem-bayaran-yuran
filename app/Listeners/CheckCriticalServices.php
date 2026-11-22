<?php

namespace App\Listeners;

use Illuminate\Foundation\Events\DiagnosingHealth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use RuntimeException;

// Dipanggil oleh route /up. Exception di sini = {"status":"down"} 500.
final class CheckCriticalServices
{
    public function handle(DiagnosingHealth $event): void
    {
        DB::select('select 1');

        $waiting = Queue::size('default');
        if ($waiting > 500) {
            throw new RuntimeException("Queue default tersekat: {$waiting} job menunggu.");
        }
    }
}
