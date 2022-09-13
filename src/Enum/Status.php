<?php

namespace App\Enum;

enum Status: string
{
    case NEW = 'new';
    case PENDING = 'pending';
    case FAILED = 'failed';
    case DONE = 'done';
}
