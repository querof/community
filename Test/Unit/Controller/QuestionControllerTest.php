<?php

declare(strict_types=1);

namespace Community\Forum\Controller;

use Community\Forum\Entity\Article;
use Community\Forum\Entity\Comment;
use Community\Forum\Entity\Community;
use Community\Forum\Entity\Question;
use Community\Forum\Entity\User;
use Community\Forum\Factories\PostFactoryInterface;
use Community\Forum\Repository\CommunityRepositoryInterface;
use Community\Forum\Repository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;

class QuestionControllerTest extends TestCase
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
    private $questionController;

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
    private $questions;

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

        $this->questions = [
            new Question('123', 'Question 1', 'Title 1',$this->users[0]),
            new Question('456', 'Question 2', 'Title 2',  $this->users[0]),
            new Question('123', 'Question 3', 'Title 3',  $this->users[2])
        ];

        $this->communities[0]->setQuestion($this->questions[0]);
        $this->communities[0]->setQuestion($this->questions[1]);
        $this->communities[2]->setQuestion($this->questions[2]);

        $this->communityRepositoryInterface->method('get')->with('1')->willReturn($this->communities[0]);


        $this->userRepositoryInterface->expects($this->any())->method("get")->with("1")->will($this->returnValue($this->users[0]));

        $this->questionController = new QuestionController(
            $this->communityRepositoryInterface,
            $this->postFactoryInterface,
            $this->userRepositoryInterface
        );
    }

    public function testRetrieveQuestionListOfACommunitySuccessfully(): void
    {
        $questionList = $this->questionController->listAction('1');

        $this->assertInstanceOf(Question::class, $questionList['123']);
        $this->assertCount(2, $questionList);
    }

    public function testRetrieveEmptyQuestionListOfACommunitySuccessfully(): void
    {
        $questionList = $this->questionController->listAction('invalid_id');

        $this->assertCount(0, $questionList);
    }

    public function testCreateQuestionSuccessfully(): void
    {
        $questionCreated = $this->questionController->createAction(
            $this->users[0]->getId(),
            $this->communities[0]->getId(),
            'Question created Title',
            'Question created'
        );

        $this->assertInstanceOf(Question::class, $questionCreated);
    }

    public function testSuccessfullyUpdatedQuestion(): void
    {
        $questionUpdated = $this->questionController->updateAction(
            $this->users[0]->getId(),
            $this->communities[0]->getId(),
            $this->questions[0]->getId(),
            'Title updated',
            'Text updated'
        );

        $this->assertInstanceOf(Question::class, $questionUpdated);
        $this->assertEquals(['Title updated', 'Text updated'], [$questionUpdated->getTitle(), $questionUpdated->getText()]);
    }

    public function testReturnNullUpdatingQuestionWhenUserIsNotAuthorOrAdmin(): void
    {
        $questionUpdated = $this->questionController->updateAction(
            $this->users[1]->getId(),
            $this->communities[0]->getId(),
            $this->questions[0]->getId(),
            'Title updated',
            'Text updated'
        );

        $this->assertNull($questionUpdated);
    }

    public function testSuccessfullyDeleteQuestion(): void
    {
        $this->questionController->deleteAction(
            $this->users[0]->getId(),
            $this->communities[0]->getId(),
            $this->questions[1]->getId()
        );

        $this->assertCount(1, $this->communities[0]->getQuestions());
    }

    public function testAvoidDeletingQuestionWhenUserIsNotAAuthorOrAdmin(): void
    {
        $this->questionController->deleteAction(
            $this->users[1]->getId(),
            $this->communities[0]->getId(),
            $this->questions[1]->getId()
        );

        $this->assertCount(2, $this->communities[0]->getQuestions());
    }

    public function testSuccessfullyAddCommentToQuestion(): void
    {
        $commentAdded = $this->questionController->commentAction(
            $this->users[0]->getId(),
            $this->communities[0]->getId(),
            $this->questions[0]->getId(),
            'Comment Added'
        );

        $this->assertInstanceOf(Comment::class, $commentAdded);
        $this->assertCount(1, $this->questions[0]->getComments());
        $this->assertEquals(current($this->questions[0]->getComments()), $commentAdded);
    }

    public function testRetrieveNullWhenAddCommentToAndInvalidQuestion(): void
    {
        $commentAdded = $this->questionController->commentAction(
            $this->users[0]->getId(),
            $this->communities[0]->getId(),
            'invalid_question_id',
            'Comment Added'
        );

        $this->assertNull($commentAdded);
    }
}
