<?php

declare(strict_types=1);

namespace App\Security\Handler;

use App\Security\Repository\CodeQueryRepositoryInterface;
use App\Security\Repository\CodeRepositoryInterface;
use App\Security\Repository\UserQueryRepositoryInterface;
use App\Security\Repository\UserRepositoryInterface;
use App\Security\Validator\PasswordMatch;
use App\Security\Validator\PasswordStrength;
use App\Security\Validator\UniqueEmail;
use App\Security\Validator\ValidationMessageEnum;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[AsMessageHandler]
final readonly class ChangeUserPasswordHandler
{
    public function __construct(
        private UserQueryRepositoryInterface $userQueryRepository,
        private UserRepositoryInterface $userRepository,
        private CodeRepositoryInterface $codeRepository,
        private CodeQueryRepositoryInterface $codeQueryRepository,
        private UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    public function __invoke(ChangeUserPasswordCommand $command): void
    {
        $user = $this->userQueryRepository->findOneByEmail($command->email);

        $user->setPassword($this->userPasswordHasher->hashPassword($user, $command->password));

        $code = $this->codeQueryRepository->findBy(['userId', $user->getId()]);

        $this->userRepository->save($user);
    }
}
