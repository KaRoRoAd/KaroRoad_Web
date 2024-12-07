<?php

declare(strict_types=1);

namespace App\Security\Handler;

use App\Security\Entity\User;
use App\Security\Repository\UserRepositoryInterface;
use App\Shared\Event\UserRegisterEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsMessageHandler]
final readonly class RegisterUserHandler
{
    public function __construct(
        private MessageBusInterface $messageBus,
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

        $user = $this->userRepository->save($user);

        $this->messageBus->dispatch(new UserRegisterEvent(
            userId: $user->getId(),
            email: $command->email,
        ));
    }
}
