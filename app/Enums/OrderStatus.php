<?php

namespace App\Enums;

enum OrderStatus: string
{
    case InProgress = 'in progress';
    case Ready      = 'ready';
    case Completed  = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::InProgress => 'In behandeling',
            self::Ready      => 'Gereed om af te halen',
            self::Completed  => 'Opgehaald',
        };
    }
}