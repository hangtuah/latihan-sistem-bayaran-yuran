<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Fpx     = 'fpx';
    case Card    = 'card';
    case EWallet = 'ewallet';
    case Counter = 'counter';   // kaunter Bendahari
}
