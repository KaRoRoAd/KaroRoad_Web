<?php

declare(strict_types=1);

namespace App\Security\Handler;

use App\Security\Entity\User;
use App\Security\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsMessageHandler]
final readonly class RegisterUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserPasswordHasherInterface $userPasswordHasher,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(RegisterUserCommand $command): void
    {
        $user = new User(
            email: $command->email,
            password: $command->password
        );

        $user->setPassword($this->userPasswordHasher->hashPassword($user, $command->password));

        $this->userRepository->save($user);
    }
}
