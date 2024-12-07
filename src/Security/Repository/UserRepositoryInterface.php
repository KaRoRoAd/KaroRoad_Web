<?php

declare(strict_types=1);

namespace App\Security\Repository;

use Symfony\Component\Security\Core\User\UserInterface;

interface UserRepositoryInterface
{
    public function save(UserInterface $User): UserInterface;
}
