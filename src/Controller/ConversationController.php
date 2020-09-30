<?php

namespace Community\Forum\Controller;

use Community\Forum\Factories\PostFactoryInterface;
use Community\Forum\Repository\CommunityRepositoryInterface;
use Community\Forum\Repository\UserRepositoryInterface;

class ConversationController
{

    /** @var CommunityRepositoryInterface */
    private $communityRepository;

    /** @var PostFactoryInterface */
    private $postFactory;

    /** @var UserRepositoryInterface */
    private $userRepository;

    public function __construct(CommunityRepositoryInterface $communityRepository, PostFactoryInterface $postFactory, UserRepositoryInterface $userRepository)
    {
        $this->communityRepository = $communityRepository;
        $this->postFactory = $postFactory;
        $this->userRepository = $userRepository;
    }

    /**
     * @param $communityId
     * @return array
     *
     * POST innercircle.com/community/[user-id]/[community-id]/conversations
     */
    public function listAction($communityId)
    {
        try {
            $community = $this->communityRepository->get($communityId);

            return $community->getConversations();
        } catch (\Exception $exception) {
            return [];
        }
    }

    /**
     * @param $communityId
     * @param $title
     * @param $text
     *
     * @return \Community\Forum\Entity\Post|null
     *
     * POST innercircle.com/community/[user-id]/[community-id]/conversations/[type]
     *
     */
    public function createAction($userId, $communityId, $title, $text)
    {
        $author = $this->userRepository->get($userId);
        $community = $this->communityRepository->get($communityId);

        $conversation = $this->postFactory->createConversation($text, $author);

        $community->setConversation($conversation);

        return $conversation;
    }

    /**
     * @param $communityId
     * @param $title
     * @param $text
     *
     * @return mixed
     *
     * PUT innercircle.com/community/[user-id]/[community-id]/conversations/[conversation-id]
     *
     */
    public function updateAction($userId, $communityId, $conversationId, $title, $text)
    {
        try {
            $author = $this->userRepository->get($userId);
            $community = $this->communityRepository->get($communityId);

            $conversation = $community->getConversation($conversationId);
            if ($author->getId() !== $conversation->getAuthor()->getId() && !$author->isAdmin()) {
                throw new \Exception('The user is not authorized to update this Conversation');
            }
            $conversation->setText($text);

            return $conversation;
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * @param $communityId
     * @param $title
     * @param $text
     *
     * @return null
     *
     * DELETE innercircle.com/community/[user-id]/[community-id]/conversations/[conversation-id]
     */
    public function deleteAction($userId, $communityId, $conversationId)
    {
        try {
            $author = $this->userRepository->get($userId);
            $community = $this->communityRepository->get($communityId);

            $conversation = $community->getConversation($conversationId);

            if ($author->getId() !== $conversation->getAuthor()->getId() && !$author->isAdmin()) {
                throw new \Exception('The user is not authorized to delete this Conversation');
            }

            $community->deleteConversation($conversationId);

            return null;
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * @param $communityId
     * @param $title
     * @param $text
     * @return mixed
     *
     * POST innercircle.com/community/[user-id]/[community-id]/conversations/[conversation-id]
     */
    public function commentAction($userId, $communityId, $conversationId, $text)
    {
        try {
            $author = $this->userRepository->get($userId);
            $community = $this->communityRepository->get($communityId);
            $comment = $this->postFactory->createComment($text, $author);

            $conversation = $community->getConversation($conversationId);

            $conversation->setComment($comment);

            return $comment;
        } catch (\Exception $exception) {
            return null;
        }
    }
}
