<?php

declare(strict_types=1);

namespace App\Security\Handler;

use App\Security\EmailCodeGenerator\EmailCodeGeneratorInterface;
use App\Security\EmailCodeGenerator\ExceptionCommunicationEnum;
use App\Security\Entity\CodeForResetPassword;
use App\Security\Repository\CodeQueryRepositoryInterface;
use App\Security\Repository\CodeRepositoryInterface;
use App\Security\Repository\UserQueryRepositoryInterface;
use App\Shared\Event\ResetUserPasswordEvent;
use InvalidArgumentException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[AsMessageHandler]
final readonly class ResetUserPasswordHandler
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private EmailCodeGeneratorInterface $emailCodeGenerator,
        private CodeRepositoryInterface $codeRepository,
        private CodeQueryRepositoryInterface $codeQueryRepository,
        private UserQueryRepositoryInterface $userRepository,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(ResetUserPasswordCommand $command): void
    {
        $token = $this->emailCodeGenerator->generate();

        $user = $this->guard($command->email);

        $code = $this->codeQueryRepository->findOneByUserId($user->getId());

        if ($code instanceof CodeForResetPassword) {
            /** @var CodeForResetPassword $code */
            $code->setCode($token);
            $code->setUseCode(false);
        } else {
            $code = new CodeForResetPassword(
                code: $token,
                userId: $user->getId(),
            );
        }
        $this->codeRepository->save($code);

        $this->messageBus->dispatch(new ResetUserPasswordEvent(
            email: $command->email,
            token: $token,
        ));
    }

    private function guard(string $email): ?UserInterface
    {
        $user = $this->userRepository->findOneByEmail($email);
        if ($user === null) {
            throw new InvalidArgumentException(ExceptionCommunicationEnum::USER_NOT_FOUND->value);
        }
        return $user;
    }
}
