<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Task\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource]
#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deadLine = null;

    /**
     * @var Collection<int, UserTask>
     */
    #[ORM\ManyToMany(targetEntity: UserTask::class, mappedBy: 'task')]
    private Collection $userTasks;

    public function __construct()
    {
        $this->userTasks = new ArrayCollection();
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

    public function getDeadLine(): ?\DateTimeInterface
    {
        return $this->deadLine;
    }

    public function setDeadLine(?\DateTimeInterface $deadLine): static
    {
        $this->deadLine = $deadLine;

        return $this;
    }

    /**
     * @return Collection<int, UserTask>
     */
    public function getUserTasks(): Collection
    {
        return $this->userTasks;
    }

    public function addUserTask(UserTask $userTask): static
    {
        if (!$this->userTasks->contains($userTask)) {
            $this->userTasks->add($userTask);
            $userTask->addTask($this);
        }

        return $this;
    }

    public function removeUserTask(UserTask $userTask): static
    {
        if ($this->userTasks->removeElement($userTask)) {
            $userTask->removeTask($this);
        }

        return $this;
    }
}
