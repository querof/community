<?php

declare(strict_types=1);

namespace Community\Forum\Repository;


use InvalidArgumentException;
use Community\Forum\Entity\User;
use Community\Forum\Lib\UniqueIdentifierGeneratorInterface;

class UserRepository implements UserRepositoryInterface
{
    /** @var array */
    private $users = [];

    /** @var UniqueIdentifierGeneratorInterface */
    private $uniqueIdentifierGenerator;

    public function __construct(UniqueIdentifierGeneratorInterface $uniqueIdentifierGenerator)
    {
        $this->uniqueIdentifierGenerator = $uniqueIdentifierGenerator;
    }

    public function add(string $username, bool $isAdmin): void
    {
        $this->users[$this->uniqueIdentifierGenerator->generate()] = new User($this->uniqueIdentifierGenerator->generate(), $username, $isAdmin);
    }

    public function get(string $id): User
    {
        if (!array_key_exists($id, $this->users)) {
            throw new InvalidArgumentException('User Id is not registered');
        }

        return $this->users[$id];
    }

    public function remove(string $id): void
    {
        unset($this->users[$id]);
    }

    public function list(): array
    {
        return $this->users;
    }
}
