<?php
declare(strict_types=1);

namespace Community\Forum\Factories;

use Community\Forum\Entity\Article;
use Community\Forum\Entity\Comment;
use Community\Forum\Entity\Conversation;
use Community\Forum\Entity\Question;
use Community\Forum\Lib\UniqueIdentifierGeneratorInterface;
use Community\Forum\Entity\User;

class PostFactory implements PostFactoryInterface
{
    /** @var UniqueIdentifierGeneratorInterface  */
    private $uniqueIdentifierGenerator;

    public function __construct(UniqueIdentifierGeneratorInterface $uniqueIdentifierGenerator)
    {
        $this->uniqueIdentifierGenerator = $uniqueIdentifierGenerator;
    }

    public function createArticle(string $text, string $title, bool $commentsDisabled, User $author): Article
    {
        return new Article($this->uniqueIdentifierGenerator->generate(), $text, $title, $commentsDisabled, $author);
    }

    public function createConversation(string $text, User $author): Conversation
    {
        return new Conversation($this->uniqueIdentifierGenerator->generate(), $text, $author);
    }

    public function createQuestion(string $text, string $title, User $author): Question
    {
        return new Question($this->uniqueIdentifierGenerator->generate(), $text, $title, $author);
    }

    public function createComment(string $text, User $author): Comment
    {
        return new Comment($this->uniqueIdentifierGenerator->generate(), $text, $author);
    }
}
