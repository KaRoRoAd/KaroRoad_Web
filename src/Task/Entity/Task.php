<?php

declare(strict_types=1);

namespace App\Task\Entity;

use App\Task\Enum\StatusEnum;
use App\Task\Repository\TaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(name: 'owner_id', type: Types::INTEGER, nullable: false)]
    private ?int $ownerId = null;

    #[ORM\Column(name: 'employee_id', type: Types::INTEGER, nullable: true)]
    private ?int $employeeId = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $deadline = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->status = StatusEnum::NEW->value;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getOwnerId(): ?int
    {
        return $this->ownerId;
    }

    public function setOwnerId(?int $ownerId): static
    {
        $this->ownerId = $ownerId;

        return $this;
    }

    public function getEmployeeId(): ?int
    {
        return $this->employeeId;
    }

    public function setEmployeeId(?int $employeeId): static
    {
        $this->employeeId = $employeeId;

        return $this;
    }

    public function getDeadline(): ?\DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(\DateTimeInterface $deadline): static
    {
        $this->deadline = $deadline;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }
}
