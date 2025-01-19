<?php

declare(strict_types=1);

namespace App\Security\Repository;

use App\Security\Entity\CodeForResetPassword;

interface CodeQueryRepositoryInterface
{
    public function findOneByUserId(int $userId): ?CodeForResetPassword;
}
