<?php

declare(strict_types=1);

namespace Community\Forum\Repository;


use Community\Forum\Entity\Community;
use InvalidArgumentException;
use Community\Forum\Lib\UniqueIdentifierGeneratorInterface;
use Community\Forum\Entity\User;

class CommunityRepository implements CommunityRepositoryInterface
{
    /** @var array */
    private $communities = [];

    /** @var UniqueIdentifierGeneratorInterface */
    private $uniqueIdentifierGenerator;

    public function __construct(UniqueIdentifierGeneratorInterface $uniqueIdentifierGenerator)
    {
        $this->uniqueIdentifierGenerator = $uniqueIdentifierGenerator;
    }


    public function add(string $name, User $moderator): void
    {
        $this->communities[$this->uniqueIdentifierGenerator->generate()] = new Community($this->uniqueIdentifierGenerator->generate(), $name, $moderator);
    }

    public function get(string $id): Community
    {
        if (!array_key_exists($id, $this->communities)) {
            throw new InvalidArgumentException('Community Id is not registered');
        }

        return $this->communities[$id];
    }

    public function remove(string $id): void
    {
        unset($this->communities[$id]);
    }

    public function list(): array
    {
        return $this->communities;
    }
}
