<?php

declare(strict_types=1);

namespace App\Firm\Exception;

enum ExceptionEnum:string
{
    case USER_HAS_FIRM = 'User has already a firm.';
    case OWNER_CAN_ONLY_CREATE_ONE_FIRM = 'Owner can only create one firm.';
}
