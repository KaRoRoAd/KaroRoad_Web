<?php

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
    private ?int $userId = null;

    #[ORM\Column]
    private ?int $meetId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

        return $this;
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
