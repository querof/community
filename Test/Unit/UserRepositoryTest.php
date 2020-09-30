<?php

declare(strict_types=1);

namespace Community\Forum\Repository;

use Community\Forum\Entity\User;
use Community\Forum\Lib\UniqueIdentifierGeneratorInterface;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $uniqueIdentifierGeneratorInterface;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function setUp():void {
        $this->uniqueIdentifierGeneratorInterface = $this->createMock(UniqueIdentifierGeneratorInterface::class);

        $this->userRepository= new UserRepository($this->uniqueIdentifierGeneratorInterface);
    }

    public function testAddUserSuccessfully(): void
    {
        $this->userRepository->add("User 1", false);

        $user = $this->userRepository->list();

        $this->assertInstanceOf(User::class, end($user));
        $this->assertCount(1, $user);
    }

    public function testGetUserSuccessfully(): void
    {
        $this->userRepository->add("User 1", false);

        $userList = $this->userRepository->list();
        $userAdded = end($userList);

        $id = $userAdded->getId();

        $user = $this->userRepository->get($id);

        $this->assertInstanceOf(User::class, $user);
    }

    public function testThrowInvalidArgumentExceptionOnGetWhenUserIsNotFound(): void
    {

        $this->expectException(\InvalidArgumentException::class);
        $this->userRepository->get('1234');

    }

    public function testRemoveUserSuccessfully(): void
    {
        $this->userRepository->add("User 1", false);

        $userList = $this->userRepository->list();
        $userAdded = end($userList);

        $id = $userAdded->getId();

        $this->userRepository->remove($id);

        $userEmptyList = $this->userRepository->list();

        $this->assertCount(0, $userEmptyList);
    }
}
