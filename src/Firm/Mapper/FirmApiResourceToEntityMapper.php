<?php

declare(strict_types=1);

namespace App\Firm\Mapper;

use App\Firm\Exception\UserHasFirmException;
use App\Shared\Owner\OwnerProviderInterface;
use App\Firm\ApiResource\FirmResource;
use App\Firm\Entity\Firm;
use App\Firm\Repository\FirmRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: FirmResource::class, to: Firm::class)]
final readonly class FirmApiResourceToEntityMapper implements MapperInterface
{
    public function __construct(
        private FirmRepositoryInterface $repository,
        private OwnerProviderInterface $ownerProvider
    ) {
    }

    public function load(object $from, string $toClass, array $context): object
    {
        $dto = $from;
        assert($dto instanceof FirmResource);

        $ownerId = $this->ownerProvider->getOwnerDto()->id;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->guard($ownerId);
        }

        return $dto->id ? $this->repository->find($dto->id) : new Firm(
            name: $dto->name,
        );
    }

    public function populate(object $from, object $to, array $context): object
    {
        $dto = $from;
        $entity = $to;
        assert($dto instanceof FirmResource);
        assert($entity instanceof Firm);

        $entity->setName($dto->name);
        $entity->setOwnerId($this->ownerProvider->getOwnerDto()->id);

        return $entity;
    }

    private function guard(int $ownerId):void
    {
        $existingFirm = $this->repository->findOneBy(['ownerId' => $ownerId]);

        if ($existingFirm) {
            throw new UserHasFirmException();
        }
    }
}

