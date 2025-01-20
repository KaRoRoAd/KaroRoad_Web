<?php

declare(strict_types=1);

namespace App\Meet\Entity;

use App\Meet\Repository\MeetsUsersRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MeetsUsersRepository::class)]
class MeetsUsers
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?string $email = null;

    #[ORM\Column]
    private ?int $meetId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getMeetId(): ?int
    {
        return $this->meetId;
    }

    public function setMeetId(int $meetId): static
    {
        $this->meetId = $meetId;

        return $this;
    }
}
