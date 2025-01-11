<?php

declare(strict_types=1);

namespace App\Security\Handler;

use App\Security\Repository\UserQueryRepositoryInterface;
use App\Security\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class AddUserToFirmHandler
{
    public function __construct(
        private UserQueryRepositoryInterface $userQueryRepository,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function __invoke(AddUserToFirmCommand $command): void
    {
        $user = $this->userQueryRepository->findOneByEmail($command->email);

        $user->setFirmId($command->firmId);

        $this->userRepository->save($user);
    }
}
