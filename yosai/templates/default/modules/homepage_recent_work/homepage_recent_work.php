<?php

use ModuleBundle\Util\ModuleInterface;

class Homepage_Recent_Work extends ModuleInterface
{
    public $name = "homepage_recent_work";
    public $displayName = "Homepage Recent Work Content";
    public $description = "The recent work block for the homepage";
    public $pages = array("front_home");

    public function install()
    {
        if (parent::install() &&
            $this->registerHook("displayContentMain"))
            return true;
        return false;
    }

    public function hook_displayContentMain()
    {
        $max = 6;
        $content_manager = $this->container->get('content.manager');
        $articles = $content_manager->getArticleManager()->get();
        $categories = $content_manager->getCategoryManager()->get();

        $articles = array_reverse(array_slice($articles, -$max, $max));
        $diff = $max - count($articles);

        $categories_hashed = array();
        foreach ($categories as $category)
            $categories_hashed[$category->getId()] = $category;

        for ($i = 0; $i < $diff; $i++)
            $articles[] = NULL;

        return array(
            "template" => __DIR__."/homepage_recent_work.html.twig",
            "params" => array(
                "articles" => $articles,
                "categories" => $categories_hashed
            )
        );
    }
}
