<?php

declare(strict_types=1);

namespace App\Task\Repository;

use App\Task\Entity\Task;

interface TaskRepositoryInterface
{
    public function save(Task $task): Task;
}
