<?php

declare(strict_types=1);

namespace Community\Forum\Entity;

class Question extends Post
{

    /** @var string  */
    private $title;

    /** @var array  */
    private $comments = [];

    /** @var User  */
    private $author;

    public function __construct(string $id, string $text, string $title, User $author)
    {
        $this->id = $id;
        $this->text = $text;
        $this->title = $title;
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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getComments(): array
    {
        return $this->comments;
    }

    public function getComment(string $id): Comment
    {
        return $this->comments[$id];
    }

    public function setComment(Comment $comment): void
    {
        $this->comments[$comment->getId()] = $comment;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }


}
