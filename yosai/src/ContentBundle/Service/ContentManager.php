<?php
namespace ContentBundle\Service;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use FOS\UserBundle\Doctrine\UserManager as FOSUserManager;
use Doctrine\ORM\EntityManager;
use ContentBundle\Util\ArticleManager;
use ContentBundle\Util\CategoryManager;
use ContentBundle\Util\UserManager;

class ContentManager
{
    private $user_manager;
    private $article_manager;
    private $category_manager;

    public function __construct(EntityManager $em, FOSUserManager $fos_um, TokenStorage $token_storage)
    {
        $this->user_manager = new UserManager($fos_um, $em, $token_storage->getToken()->getUser());
        $this->article_manager = new ArticleManager($em);
        $this->category_manager = new CategoryManager($em);
    }

    public function getUserManager()
    {
        return $this->user_manager;
    }

    public function getArticleManager()
    {
        return $this->article_manager;
    }

    public function getCategoryManager()
    {
        return $this->category_manager;
    }
}
