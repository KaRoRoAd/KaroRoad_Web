<?php

declare(strict_types=1);

namespace App\Mailing\Sender;

use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\RawMessage;

final class DevNullMailer implements MailerInterface
{
    public function send(RawMessage $message, ?Envelope $envelope = null): void
    {
    }
}
