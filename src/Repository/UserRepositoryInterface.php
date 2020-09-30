<?php

declare(strict_types=1);

namespace Community\Forum\Repository;

use Community\Forum\Entity\User;

interface UserRepositoryInterface
{
    public function add(string $username, bool $isAdmin): void;

    public function get(string $id): User;

    public function remove(string $id):void;

    public function list(): array;
}
