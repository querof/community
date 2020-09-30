<?php

namespace Community\Forum\Controller;

use Community\Forum\Factories\PostFactoryInterface;
use Community\Forum\Repository\CommunityRepositoryInterface;
use Community\Forum\Repository\UserRepositoryInterface;

class QuestionController
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
     * POST innercircle.com/community/[user-id]/[community-id]/questions
     */
    public function listAction($communityId)
    {

        try {
            $community = $this->communityRepository->get($communityId);

            return $community->getQuestions();
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
     * POST innercircle.com/community/[user-id]/[community-id]/questions/[type]
     *
     */
    public function createAction($userId, $communityId, $title, $text)
    {
        $author = $this->userRepository->get($userId);
        $community = $this->communityRepository->get($communityId);

        $question = $this->postFactory->createQuestion($text, $title, $author);

        $community->setQuestion($question);

        return $question;
    }

    /**
     * @param $communityId
     * @param $title
     * @param $text
     *
     * @return mixed
     *
     * PUT innercircle.com/community/[user-id]/[community-id]/questions/[question-id]
     *
     */
    public function updateAction($userId, $communityId, $questionId, $title, $text)
    {
        try {
            $author = $this->userRepository->get($userId);
            $community = $this->communityRepository->get($communityId);

            $question = $community->getQuestion($questionId);
            if ($author->getId() !== $question->getAuthor()->getId() && !$author->isAdmin()) {
                throw new \Exception('The user is not authorized to update this Conversation');
            }
            $question->setText($text);
            $question->setTitle($title);

            return $question;
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
     * DELETE innercircle.com/community/[user-id]/[community-id]/questions/[question-id]
     */
    public function deleteAction($userId, $communityId, $questionId)
    {
        try {
            $author = $this->userRepository->get($userId);
            $community = $this->communityRepository->get($communityId);

            $question = $community->getQuestion($questionId);

            if ($author->getId() !== $question->getAuthor()->getId() && !$author->isAdmin()) {
                throw new \Exception('The user is not authorized to delete this Conversation');
            }

            $community->deleteQuestion($questionId);

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
     * POST innercircle.com/community/[user-id]/[community-id]/questions/[question-id]
     */
    public function commentAction($userId, $communityId, $questionId, $text)
    {
        try {
            $author = $this->userRepository->get($userId);
            $community = $this->communityRepository->get($communityId);
            $comment = $this->postFactory->createComment($text, $author);

            $question = $community->getQuestion($questionId);

            $question->setComment($comment);

            return $comment;
        } catch (\Exception $exception) {
            return null;
        }
    }
}
