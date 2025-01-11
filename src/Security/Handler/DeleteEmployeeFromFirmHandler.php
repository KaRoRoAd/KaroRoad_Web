<?php

declare(strict_types=1);

namespace App\Security\Handler;

use App\Security\Repository\UserQueryRepositoryInterface;
use App\Security\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class DeleteEmployeeFromFirmHandler
{
    public function __construct(
        private UserQueryRepositoryInterface $userQueryRepository,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function __invoke(DeleteEmployeeFromFirmCommand $command): void
    {
        $user = $this->userQueryRepository->findOneByEmail($command->email);

        $user->setFirmId(null);

        $this->userRepository->save($user);
    }
}
