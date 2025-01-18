<?php

declare(strict_types=1);

namespace App\Mailing\Sender;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

abstract class Mailing
{
    public function __construct(
        #[Autowire(service: DevNullMailer::class)]
        protected MailerInterface $mailer,
        #[Autowire(env: 'string:MAILING_FROM')]
        protected string $from
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function send(string $to, array $data = []): void
    {
        $email = (new TemplatedEmail())
            ->from($this->from)
            ->to($to)
            ->subject($this->getSubject($data))
            ->htmlTemplate($this->getTemplate())
            ->context($data);

        $this->mailer->send($email);
    }

    abstract protected function getTemplate(): string;

    abstract protected function getSubject(array $data = []): string;
}
