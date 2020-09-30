<?php

namespace Community\Forum\Controller;

use Exception;
use Community\Forum\Factories\PostFactoryInterface;
use Community\Forum\Repository\CommunityRepositoryInterface;
use Community\Forum\Repository\UserRepositoryInterface;
use InvalidArgumentException;

class ArticleController
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
     * POST innercircle.com/community/[user-id]/[community-id]/articles
     */
    public function listAction($communityId)
    {
        try {
            $community = $this->communityRepository->get($communityId);

            return $community->getArticles();
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
     * POST innercircle.com/community/[user-id]/[community-id]/articles/[type]
     *
     */
    public function createAction($userId, $communityId, $title, $text)
    {
        $author = $this->userRepository->get($userId);
        $community = $this->communityRepository->get($communityId);

        $article = $this->postFactory->createArticle($text, $title, false, $author);

        $community->setArticle($article);

        return $article;
    }

    /**
     * @param $communityId
     * @param $title
     * @param $text
     *
     * @return mixed
     *
     * PUT innercircle.com/community/[user-id]/[community-id]/articles/[article-id]
     *
     */
    public function updateAction($userId, $communityId, $articleId, $title, $text)
    {
        try {
            $author = $this->userRepository->get($userId);
            $community = $this->communityRepository->get($communityId);

            $article = $community->getArticle($articleId);
            if ($author->getId() !== $article->getAuthor()->getId() && !$author->isAdmin()) {
                throw new \Exception('The user is not authorized to update this Article');
            }
            $article->setText($text);
            $article->setTitle($title);

            return $article;
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
     * DELETE innercircle.com/community/[user-id]/[community-id]/articles/[article-id]
     */
    public function deleteAction($userId, $communityId, $articleId)
    {
        try {
            $author = $this->userRepository->get($userId);
            $community = $this->communityRepository->get($communityId);

            $article = $community->getArticle($articleId);

            if ($author->getId() !== $article->getAuthor()->getId() && !$author->isAdmin()) {
                throw new \Exception('The user is not authorized to delete this Article');
            }

            $community->deleteArticle($articleId);

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
     * POST innercircle.com/community/[user-id]/[community-id]/articles/[article-id]
     */
    public function commentAction($userId, $communityId, $articleId, $text)
    {
        try {
            $author = $this->userRepository->get($userId);
            $community = $this->communityRepository->get($communityId);
            $comment = $this->postFactory->createComment($text, $author);

            $article = $community->getArticle($articleId);

            $article->setComment($comment);

            return $comment;
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * @param $communityId
     * @param $articleId
     *
     * PATCH innercircle.com/community/[community-id]/articles/[article-id]/disableComments
     */
    public function disableCommentsAction($communityId, $articleId)
    {
        try {
            $community = $this->communityRepository->get($communityId);
            $article = $community->getArticle($articleId);

            $article->setCommentsDisabled(true);

        } catch (Exception $exception) {
            return null;
        }
    }
}
