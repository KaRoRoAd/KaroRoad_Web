<?php

declare(strict_types=1);

namespace App\Mailing\Handler;

use App\Mailing\Sender\MailingInterface;
use App\Mailing\Sender\ResetPasswordMailing;
use App\Shared\Event\ResetUserPasswordEvent;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ResetPasswordMailingHandler
{
    public function __construct(
        #[Autowire(service: ResetPasswordMailing::class)]
        private MailingInterface $mailing
    ) {
    }

    public function __invoke(ResetUserPasswordEvent $event): void
    {
        $this->mailing->send($event->email, [
            'userEmail' => $event->email,
            'token' => $event->token,
        ]);
    }
}
