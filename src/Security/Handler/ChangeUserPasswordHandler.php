<?php

declare(strict_types=1);

namespace App\Security\Handler;

use App\Security\EmailCodeGenerator\ExceptionCommunicationEnum;
use App\Security\Entity\CodeForResetPassword;
use App\Security\Repository\CodeQueryRepositoryInterface;
use App\Security\Repository\CodeRepositoryInterface;
use App\Security\Repository\UserQueryRepositoryInterface;
use App\Security\Repository\UserRepositoryInterface;
use InvalidArgumentException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;

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
        $user = $this->guard($command->email);

        $code = $this->codeQueryRepository->findOneBy(['code' => $command->code]);

        if ($code instanceof CodeForResetPassword) {
            /** @var CodeForResetPassword $code */
            $code->setUseCode(true);
        } else {
            throw new InvalidArgumentException('Code not found');
        }
        $this->codeRepository->save($code);

        $user->setPassword($this->userPasswordHasher->hashPassword($user, $command->password));

        $this->userRepository->save($user);
    }

    private function guard(string $email): ?UserInterface
    {
        $user = $this->userQueryRepository->findOneByEmail($email);
        if ($user === null) {
            throw new InvalidArgumentException(ExceptionCommunicationEnum::USER_NOT_FOUND->value);
        }
        return $user;
    }
}
