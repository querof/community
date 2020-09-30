<?php

declare(strict_types=1);

namespace Community\Forum\Repository;

use Community\Forum\Entity\Community;
use Community\Forum\Entity\User;
use Community\Forum\Lib\UniqueIdentifierGeneratorInterface;
use PHPUnit\Framework\TestCase;

class CommunityRepositoryTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $uniqueIdentifierGeneratorInterface;

    /**
     * @var CommunityRepository
     */
    private $communityRepository;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $moderator;

    public function setUp():void {

        $this->moderator = $this->createMock(User::class);
        $this->uniqueIdentifierGeneratorInterface = $this->createMock(UniqueIdentifierGeneratorInterface::class);

        $this->communityRepository= new CommunityRepository($this->uniqueIdentifierGeneratorInterface);
    }

    public function testAddCommunitySuccessfully(): void
    {
        $this->communityRepository->add("Community 1", $this->moderator);

        $community = $this->communityRepository->list();

        $this->assertInstanceOf(Community::class, end($community));
        $this->assertCount(1, $community);
    }

    public function testGetCommunitySuccessfully(): void
    {
        $this->communityRepository->add("Community 1", $this->moderator);

        $communityList = $this->communityRepository->list();

        $communityAdded = end($communityList);

        $id = $communityAdded->getId();

        $community = $this->communityRepository->get($id);

        $this->assertInstanceOf(Community::class, $community);
    }

    public function testThrowInvalidArgumentExceptionOnGetWhenCommunityIsNotFound(): void
    {

        $this->expectException(\InvalidArgumentException::class);
        $this->communityRepository->get('1234');

    }

    public function testRemoveCommunitySuccessfully(): void
    {
        $this->communityRepository->add("Community 1", $this->moderator);

        $communityList = $this->communityRepository->list();
        $communityAdded = end($communityList);

        $id = $communityAdded->getId();

        $this->communityRepository->remove($id);

        $communityEmptyList = $this->communityRepository->list();

        $this->assertCount(0, $communityEmptyList);
    }
}
