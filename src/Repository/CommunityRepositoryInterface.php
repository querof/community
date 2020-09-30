<?php

declare(strict_types=1);

namespace Community\Forum\Repository;

use Community\Forum\Entity\Community;
use Community\Forum\Entity\User;

interface CommunityRepositoryInterface
{
    public function add(string $name, User $moderator): void;

    public function get(string $id): Community;

    public function remove(string $id):void;

    public function list(): array;
}
