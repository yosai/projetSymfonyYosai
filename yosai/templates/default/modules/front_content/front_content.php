<?php

use ModuleBundle\Util\ModuleInterface;

class Front_Content extends ModuleInterface
{
    public $name = "front_content";
    public $displayName = "Front Content";
    public $description = "Front content manager module";
    public $pages = array(
        "front_home",
        "front_category",
        "front_search"
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

        $categories_hashed = array();
        foreach ($categories as $category)
            $categories_hashed[$category->getId()] = $category;

        if ($this->route == "front_home") {
            $max = 4;
            $diff = 0;

            if (count($articles) > 6) {
                $articles = array_reverse(array_slice($articles, -(6+$max), $max));
                $diff = $max - count($articles);
            }
            else {
                $articles = array();
                $diff = 4;
            }

            for ($i = 0; $i < $diff; $i++)
                $articles[] = NULL;
        } else if ($this->route == "front_category") {
            $category = explode('-', $this->route_params["slug"]);

            foreach ($articles as $key => $article)
                if ($article->getCategoryId() != $category[0])
                    unset($articles[$key]);
            array_reverse($articles);

            $min = 6;
            $diff = $min - count($articles);
            for ($i = 0; $i < $diff; $i++)
                $articles[] = NULL;
        } else if ($this->route == "front_search") {
            $request = $this->container->get('request_stack')->getCurrentRequest();
            $query = $request->query->get('q');

            $min = 6;
            $diff = $min - count($articles);
            for ($i = 0; $i < $diff; $i++)
                $articles[] = NULL;
        }

        return array(
            "template" => __DIR__."/front_content.html.twig",
            "params" => array(
                "articles" => $articles,
                "categories" => $categories_hashed
            )
        );
    }
}
