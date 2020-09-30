<?php

declare(strict_types=1);

namespace Community\Forum\Factories;

use Community\Forum\Entity\Article;
use Community\Forum\Entity\Comment;
use Community\Forum\Entity\Conversation;
use Community\Forum\Entity\Question;
use Community\Forum\Entity\User;

interface PostFactoryInterface
{
    public function createArticle(string $text, string $title, bool $commentsDisabled, User $author): Article;

    public function createConversation(string $text, User $author): Conversation;

    public function createQuestion(string $text, string $title, User $author): Question;

    public function createComment(string $text, User $author): Comment;
}
