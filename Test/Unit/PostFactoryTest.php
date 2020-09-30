<?php

declare(strict_types=1);

namespace Community\Forum\Factories;

use Community\Forum\Entity\Article;
use Community\Forum\Entity\Comment;
use Community\Forum\Entity\Conversation;
use Community\Forum\Entity\Question;
use Community\Forum\Entity\User;
use Community\Forum\Lib\UniqueIdentifierGeneratorInterface;
use PHPUnit\Framework\TestCase;

class PostFactoryTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $uniqueIdentifierGeneratorInterface;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $author;

    /**
     * @var PostFactory
     */
    private $postFactory;

    public function setUp():void {
        $this->uniqueIdentifierGeneratorInterface = $this->createMock(UniqueIdentifierGeneratorInterface::class);

        $this->author = $this->createMock(User::class);

        $this->postFactory = new PostFactory($this->uniqueIdentifierGeneratorInterface);
    }

    public function testCreateArticleSuccessfully(): void
    {
        $article = $this->postFactory->createArticle("Article 1", "Title Article 1",false, $this->author);

        $this->assertInstanceOf(Article::class, $article);
    }

    public function testCreateConversationSuccessfully(): void
    {
        $conversation = $this->postFactory->createConversation("Article 1",  $this->author);

        $this->assertInstanceOf(Conversation::class, $conversation);
    }

    public function testCreateQuestionSuccessfully(): void
    {
        $article = $this->postFactory->createQuestion("Article 1", "Title Article 1", $this->author);

        $this->assertInstanceOf(Question::class, $article);
    }

    public function testCreateCommentSuccessfully(): void
    {
        $conversation = $this->postFactory->createComment("Comment 1",  $this->author);

        $this->assertInstanceOf(Comment::class, $conversation);
    }
}
