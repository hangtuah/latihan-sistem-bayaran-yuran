<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('bursary', fn (User $user) => $user->isBursary());
