<?php

use ModuleBundle\Util\ModuleInterface;

class Admin_Stats_Articles extends ModuleInterface
{
    public $name = "admin_stats_articles";
    public $displayName = "Admin Homepage Articles Statistics";
    public $description = "Administrator panel articles statistics";
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
        $hashed_categories = array();

        foreach ($categories as $category)
            $hashed_categories[$category->getId()] = $category;

        return array(
            "template" => __DIR__."/admin_stats_articles.html.twig",
            "params" => array(
                "articles" => $articles,
                "categories" => $hashed_categories
            )
        );
    }
}
