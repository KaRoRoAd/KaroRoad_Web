<?php

declare(strict_types=1);

namespace App\Security\Repository;

use Symfony\Component\Security\Core\User\UserInterface;

interface UserQueryRepositoryInterface
{
    public function existsEmail(string $email): bool;

    public function findOneByEmail(string $email): ?UserInterface;

    public function getUserId(string $email): ?int;
}
