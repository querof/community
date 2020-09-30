<?php

namespace Community\Forum\Entity;

use InvalidArgumentException;

class Community
{
    /** @var string */
    private $id;

    /** @var string */
    private $name;

    /** @var User */
    private $moderator;

    /** @var array */
    private $articles = [];

    /** @var array */
    private $conversations = [];

    /** @var array */
    private $questions = [];

    public function __construct(string $id, string $name, User $moderator)
    {
        $this->id = $id;
        $this->name = $name;
        $this->moderator = $moderator;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getModerator(): User
    {
        return $this->moderator;
    }

    public function getArticles(): array
    {
        return $this->articles;
    }

    public function getArticle(string $id): Article
    {
        if (!array_key_exists($id, $this->articles)) {
            throw new InvalidArgumentException('Article Id is not registered');
        }

        return $this->articles[$id];
    }

    public function setArticle(Article $article): void
    {
        $this->articles[$article->getId()] = $article;
    }

    public function deleteArticle(string $id): void
    {
        unset($this->articles[$id]);
    }

    public function getConversations(): array
    {
        return $this->conversations;
    }

    public function getConversation(string $id): Conversation
    {
        if (!array_key_exists($id, $this->conversations)) {
            throw new InvalidArgumentException('Article Id is not registered');
        }

        return $this->conversations[$id];
    }

    public function setConversation(Conversation $conversation): void
    {
        $this->conversations[$conversation->getId()] = $conversation;
    }

    public function deleteConversation(string $id): void
    {
        unset($this->conversations[$id]);
    }

    public function getQuestions(): array
    {
        return $this->questions;
    }

    public function getQuestion(string $id): Question
    {
        if (!array_key_exists($id, $this->questions)) {
            throw new InvalidArgumentException('Article Id is not registered');
        }

        return $this->questions[$id];
    }

    public function setQuestion(Question $question): void
    {
        $this->questions[$question->getId()] = $question;
    }

    public function deleteQuestion(string $id): void
    {
        unset($this->questions[$id]);
    }

}
