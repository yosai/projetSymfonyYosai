<?php

use ModuleBundle\Util\ModuleInterface;

class Admin_Stats extends ModuleInterface
{
    public $name = "admin_stats";
    public $displayName = "Admin Homepage Statistics";
    public $description = "Administrator panel statistics";
    public $pages = array(
        "admin_home"
    );

    public function install()
    {
        if (parent::install() &&
            $this->registerHook("displayContentMain"))
            return true;
        return false;
    }

    public function hook_displayContentMain()
    {
        $content_manager = $this->container->get('content.manager');
        $articles = $content_manager->getArticleManager()->get();
        $categories = $content_manager->getCategoryManager()->get();
        $users = $content_manager->getUserManager()->get();

        $count_articles = count($articles);
        $count_categories = count($categories);
        $count_users = count($users);

        $last_article = end($articles)->getCreatedAt();

        $average_user = $count_articles / $count_users;
        $average_category = $count_articles / $count_categories;

        return array(
            "template" => __DIR__."/admin_stats.html.twig",
            "params" => array(
                "users" => $count_users,
                "articles" => $count_articles,
                "categories" => $count_categories,
                "average_user" => $average_user,
                "average_category" => $average_category,
                "last_article" => $last_article
            )
        );
    }
}
