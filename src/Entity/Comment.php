<?php

declare(strict_types=1);

namespace Community\Forum\Entity;

class Comment
{
    /** @var string */
    private $id;

    /** @var string */
    private $text;

    /** @var User */
    private $author;

    public function __construct(string $id, string $text, User $author)
    {
        $this->id = $id;
        $this->text = $text;
        $this->author = $author;
    }


    public function getId(): string
    {
        return $this->id;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }


}
