<?php

declare(strict_types=1);

namespace Community\Forum\Controller;

use Community\Forum\Entity\Comment;
use Community\Forum\Entity\Community;
use Community\Forum\Entity\Conversation;
use Community\Forum\Entity\User;
use Community\Forum\Factories\PostFactoryInterface;
use Community\Forum\Repository\CommunityRepositoryInterface;
use Community\Forum\Repository\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;

class ConversationControllerTest extends TestCase
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
    private $conversationController;

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
    private $conversations;

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

        $this->conversations = [
            new Conversation('123', 'Conversation 1',  $this->users[0]),
            new Conversation('456', 'Conversation 2', $this->users[0]),
            new Conversation('123', 'Conversation 3', $this->users[2])
        ];

        $this->communities[0]->setConversation($this->conversations[0]);
        $this->communities[0]->setConversation($this->conversations[1]);
        $this->communities[2]->setConversation($this->conversations[2]);

        $this->communityRepositoryInterface->method('get')->with('1')->willReturn($this->communities[0]);


        $this->userRepositoryInterface->expects($this->any())->method("get")->with("1")->will($this->returnValue($this->users[0]));

        $this->conversationController = new ConversationController(
            $this->communityRepositoryInterface,
            $this->postFactoryInterface,
            $this->userRepositoryInterface
        );
    }

    public function testRetrieveConversationListOfACommunitySuccessfully(): void
    {
        $conversationList = $this->conversationController->listAction('1');

        $this->assertInstanceOf(Conversation::class, $conversationList['123']);
        $this->assertCount(2, $conversationList);
    }

    public function testRetrieveEmptyConversationListOfACommunitySuccessfully(): void
    {
        $conversationList = $this->conversationController->listAction('invalid_id');

        $this->assertCount(0, $conversationList);
    }

    public function testCreateConversationSuccessfully(): void
    {
        $conversationCreated = $this->conversationController->createAction(
            $this->users[0]->getId(),
            $this->communities[0]->getId(),
            'Conversation created Title',
            'Conversation created'
        );

        $this->assertInstanceOf(Conversation::class, $conversationCreated);
    }

    public function testSuccessfullyUpdatedConversation(): void
    {
        $conversationUpdated = $this->conversationController->updateAction(
            $this->users[0]->getId(),
            $this->communities[0]->getId(),
            $this->conversations[0]->getId(),
            'Title updated',
            'Text updated'
        );

        $this->assertInstanceOf(Conversation::class, $conversationUpdated);
        $this->assertEquals(['Text updated'], [$conversationUpdated->getText()]);
    }

    public function testReturnNullUpdatingConversationWhenUserIsNotAuthorOrAdmin(): void
    {
        $conversationUpdated = $this->conversationController->updateAction(
            $this->users[1]->getId(),
            $this->communities[0]->getId(),
            $this->conversations[0]->getId(),
            'Title updated',
            'Text updated'
        );

        $this->assertNull($conversationUpdated);
    }

    public function testSuccessfullyDeleteConversation(): void
    {
        $this->conversationController->deleteAction(
            $this->users[0]->getId(),
            $this->communities[0]->getId(),
            $this->conversations[1]->getId()
        );

        $this->assertCount(1, $this->communities[0]->getConversations());
    }

    public function testAvoidDeletingConversationWhenUserIsNotAAuthorOrAdmin(): void
    {
        $this->conversationController->deleteAction(
            $this->users[1]->getId(),
            $this->communities[0]->getId(),
            $this->conversations[1]->getId()
        );

        $this->assertCount(2, $this->communities[0]->getConversations());
    }

    public function testSuccessfullyAddCommentToConversation(): void
    {
        $commentAdded = $this->conversationController->commentAction(
            $this->users[0]->getId(),
            $this->communities[0]->getId(),
            $this->conversations[0]->getId(),
            'Comment Added'
        );

        $this->assertInstanceOf(Comment::class, $commentAdded);
        $this->assertCount(1, $this->conversations[0]->getComments());
        $this->assertEquals(current($this->conversations[0]->getComments()), $commentAdded);
    }

    public function testRetrieveNullWhenAddCommentToAndInvalidConversation(): void
    {
        $commentAdded = $this->conversationController->commentAction(
            $this->users[0]->getId(),
            $this->communities[0]->getId(),
            'invalid_conversation_id',
            'Comment Added'
        );

        $this->assertNull($commentAdded);
    }
}
