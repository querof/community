<?php

namespace Community\Forum\Entity;

class User
{
    /** @var string */
    private $id;

    /** @var string */
    private $username;

    /** @var bool */
    private $isAdmin;

    public function __construct(string $id, string $username, bool $isAdmin)
    {
        $this->id = $id;
        $this->username = $username;
        $this->isAdmin = $isAdmin;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(bool $isAdmin): void
    {
        $this->isAdmin = $isAdmin;
    }

    public function getId(): string
    {
        return $this->id;
    }

}
