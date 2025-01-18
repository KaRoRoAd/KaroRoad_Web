<?php

declare(strict_types=1);

namespace App\Security\Repository;

use App\Security\Entity\CodeForResetPassword;

interface CodeRepositoryInterface
{
    public function save(CodeForResetPassword $code): ?CodeForResetPassword;
}
