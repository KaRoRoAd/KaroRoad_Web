<?php

declare(strict_types=1);

namespace App\Security\Enum;

enum RoleEnum: string
{
    case ROLE_USER = 'ROLE_USER';
    case ROLE_MANAGER = 'ROLE_MANAGER';
    case ROLE_ADMIN = 'ROLE_ADMIN';
}
