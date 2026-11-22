<?php

use Illuminate\Support\Facades\Queue;

it('/up lapor up bila semua sihat', function () {
    $this->get('/up')->assertOk();
});

it('/up lapor down bila queue tersekat', function () {
    Queue::shouldReceive('size')->with('default')->andReturn(501);

    $this->withoutExceptionHandling(except: [RuntimeException::class])
        ->get('/up')->assertStatus(500);
})->skip('Pilihan: cuba sendiri dalam Lab 4');
