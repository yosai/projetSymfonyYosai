<?php

use ModuleBundle\Util\ModuleInterface;

class Front_Article extends ModuleInterface
{
    public $name = "front_article";
    public $displayName = "Front Article";
    public $description = "Front article display module";
    public $pages = array(
        "front_article"
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
        $article_info = explode("-", $this->route_params["slug"]);
        $content_manager = $this->container->get('content.manager');
        $categories = $content_manager->getCategoryManager()->get();
        $article = $content_manager->getArticleManager()->get($article_info[0]);

        $categories_hashed = array();
        foreach ($categories as $category)
            $categories_hashed[$category->getId()] = $category;

        return array(
            "template" => __DIR__."/front_article.html.twig",
            "params" => array(
                "article" => $article,
                "categories" => $categories_hashed
            )
        );
    }
}
