<?php

declare(strict_types=1);

namespace Community\Forum\Controller;

use Community\Forum\Entity\Article;
use Community\Forum\Entity\Comment;
use Community\Forum\Entity\Community;
use Community\Forum\Entity\User;
use Community\Forum\Factories\PostFactoryInterface;
use Community\Forum\Repository\CommunityRepositoryInterface;
use Community\Forum\Repository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;

class ArticleControllerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $postFactoryInterface;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $communityRepositoryInterface;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $userRepositoryInterface;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $articleController;

    /**
     * @var array
     */
    private $users;

    /**
     * @var array
     */
    private $communities;

    /**
     * @var array
     */
    private $articles;

    public function setUp()
    {
        $this->userRepositoryInterface = $this->createMock(UserRepositoryInterface::class);
        $this->postFactoryInterface = $this->createMock(PostFactoryInterface::class);
        $this->communityRepositoryInterface = $this->createMock(CommunityRepositoryInterface::class);

        $this->users = [
            new User('1', 'User 1', false),
            new User('2', 'User 2', false),
            new User('3', 'User 3', true),
        ];

        $this->communities = [
            new Community('1', 'Community 1', $this->users[0]),
            new Community('2', 'Community 2', $this->users[1]),
            new Community('3', 'Community 3', $this->users[2]),
        ];

        $this->articles = [
            new Article('123', 'Article 1', 'Title 1', false, $this->users[0]),
            new Article('456', 'Article 2', 'Title 2', false, $this->users[0]),
            new Article('123', 'Article 3', 'Title 3', true, $this->users[2])
        ];

        $this->communities[0]->setArticle($this->articles[0]);
        $this->communities[0]->setArticle($this->articles[1]);
        $this->communities[2]->setArticle($this->articles[2]);

        $this->communityRepositoryInterface->method('get')->with('1')->willReturn($this->communities[0]);


        $this->userRepositoryInterface->expects($this->any())->method("get")->with("1")->will($this->returnValue($this->users[0]));

        $this->articleController = new ArticleController(
            $this->communityRepositoryInterface,
            $this->postFactoryInterface,
            $this->userRepositoryInterface
        );
    }

    public function testRetrieveArticleListOfACommunitySuccessfully(): void
    {
        $articleList = $this->articleController->listAction('1');

        $this->assertInstanceOf(Article::class, $articleList['123']);
        $this->assertCount(2, $articleList);
    }

    public function testRetrieveEmptyArticleListOfACommunitySuccessfully(): void
    {
        $articleList = $this->articleController->listAction('invalid_id');

        $this->assertCount(0, $articleList);
    }

    public function testCreateArticleSuccessfully(): void
    {
        $articleCreated = $this->articleController->createAction(
            $this->users[0]->getId(),
            $this->communities[0]->getId(),
            'Article created Title',
            'Article created'
        );

        $this->assertInstanceOf(Article::class, $articleCreated);
    }

    public function testSuccessfullyUpdatedArticle(): void
    {
        $articleUpdated = $this->articleController->updateAction(
            $this->users[0]->getId(),
            $this->communities[0]->getId(),
            $this->articles[0]->getId(),
            'Title updated',
            'Text updated'
        );

        $this->assertInstanceOf(Article::class, $articleUpdated);
        $this->assertEquals(['Title updated', 'Text updated'], [$articleUpdated->getTitle(), $articleUpdated->getText()]);
    }

    public function testReturnNullUpdatingArticleWhenUserIsNotAuthorOrAdmin(): void
    {
        $articleUpdated = $this->articleController->updateAction(
            $this->users[1]->getId(),
            $this->communities[0]->getId(),
            $this->articles[0]->getId(),
            'Title updated',
            'Text updated'
        );

        $this->assertNull($articleUpdated);
    }

    public function testSuccessfullyDeleteArticle(): void
    {
        $this->articleController->deleteAction(
            $this->users[0]->getId(),
            $this->communities[0]->getId(),
            $this->articles[1]->getId()
        );

        $this->assertCount(1, $this->communities[0]->getArticles());
    }

    public function testAvoidDeletingArticleWhenUserIsNotAAuthorOrAdmin(): void
    {
        $this->articleController->deleteAction(
            $this->users[1]->getId(),
            $this->communities[0]->getId(),
            $this->articles[1]->getId()
        );

        $this->assertCount(2, $this->communities[0]->getArticles());
    }

    public function testSuccessfullyAddCommentToArticle(): void
    {
        $commentAdded = $this->articleController->commentAction(
            $this->users[0]->getId(),
            $this->communities[0]->getId(),
            $this->articles[0]->getId(),
            'Comment Added'
        );

        $this->assertInstanceOf(Comment::class, $commentAdded);
        $this->assertCount(1, $this->articles[0]->getComments());
        $this->assertEquals(current($this->articles[0]->getComments()), $commentAdded);
    }

    public function testRetrieveNullWhenAddCommentToAndInvalidArticle(): void
    {
        $commentAdded = $this->articleController->commentAction(
            $this->users[0]->getId(),
            $this->communities[0]->getId(),
            'invalid_article_id',
            'Comment Added'
        );

        $this->assertNull($commentAdded);
    }

    public function testSuccessfullyDisableCommentsToAnArticle(): void
    {
        $this->articleController->commentAction(
            $this->users[0]->getId(),
            $this->communities[0]->getId(),
            $this->articles[0]->getId(),
            'Comment Added'
        );

        $this->articleController->disableCommentsAction(
            $this->users[0]->getId(),
            $this->articles[0]->getId()
        );

        $commentNotAdded = $this->articleController->commentAction(
            $this->users[0]->getId(),
            $this->communities[0]->getId(),
            $this->articles[0]->getId(),
            'Comment Not Added'
        );

        $this->assertNull($commentNotAdded);
        $this->assertCount(1, $this->articles[0]->getComments());
    }
}
