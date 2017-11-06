<?php

use ModuleBundle\Util\ModuleInterface;

class Admin_Stats_Categories extends ModuleInterface
{
    public $name = "admin_stats_categories";
    public $displayName = "Admin Homepage Categories Statistics";
    public $description = "Administrator panel categories statistics";
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
        $categories = $content_manager->getCategoryManager()->get();
        $articles = $content_manager->getArticleManager()->get();
        $categories_counter = array();
        $categories_hashed = array();

        foreach ($categories as $category) {
            $categories_hashed[$category->getId()] = $category->getName();
            $categories_counter[$category->getName()] = 0;
        }

        foreach($articles as $article) {
            $category_name = $categories_hashed[$article->getCategoryId()];
            $categories_counter[$category_name]++;
        }
        arsort($categories_counter);

        return array(
            "template" => __DIR__."/admin_stats_categories.html.twig",
            "params" => array(
                "categories" => $categories_counter
            )
        );
    }
}
