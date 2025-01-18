<?php

declare(strict_types=1);

namespace App\Mailing\Sender;

final class ResetPasswordMailing extends Mailing implements MailingInterface
{
    protected function getTemplate(): string
    {
        return '@mailing/reset_user_password_email.html.twig';
    }

    protected function getSubject(array $data = []): string
    {
        return 'Zmiana hasła';
    }
}
