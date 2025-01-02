<?php

declare(strict_types=1);

namespace App\Task\Enum;

enum StatusEnum: string
{
    case NEW = 'new';
    case IN_PROGRESS = 'in_progress';
    case DONE = 'done';
}
