<?php

declare(strict_types=1);

namespace App\Security\Handler;

use App\Security\EmailCodeGenerator\EmailCodeGeneratorInterface;
use App\Security\Entity\CodeForResetPassword;
use App\Security\Repository\CodeRepositoryInterface;
use App\Security\Repository\UserQueryRepositoryInterface;
use App\Shared\Event\ResetUserPasswordEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final readonly class ResetUserPasswordHandler
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private EmailCodeGeneratorInterface $emailCodeGenerator,
        private CodeRepositoryInterface $codeQueryRepository,
        private UserQueryRepositoryInterface $userRepository,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(ResetUserPasswordCommand $command): void
    {
        $token = $this->emailCodeGenerator->generate();

        $user = $this->userRepository->findOneByEmail($command->email);

        $code = new CodeForResetPassword(
            code: $token,
            userId: $user->getId(),
        );

        $this->codeQueryRepository->save($code);


        $this->messageBus->dispatch(new ResetUserPasswordEvent(
            email: $command->email,
            token: $this->emailCodeGenerator->generate(),
        ));
    }
}
