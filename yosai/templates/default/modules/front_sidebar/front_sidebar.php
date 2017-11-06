<?php

use ModuleBundle\Util\ModuleInterface;

class Front_Sidebar extends ModuleInterface
{
    public $name = "front_sidebar";
    public $displayName = "Front Sidebar";
    public $description = "Front sidebar module";
    public $pages = array(
        "front_home",
        "front_category",
        "front_article",
        "front_search",
    );

    public function install()
    {
        if (parent::install() &&
            $this->registerHook("displayContentSidebar"))
            return true;
        return false;
    }

    public function hook_displayContentSidebar()
    {
        $content_manager = $this->container->get('content.manager');
        $articles = $content_manager->getArticleManager()->get();
        $categories = $content_manager->getCategoryManager()->get();
        $max = 3;

        // Categories
        $categories_count = array();
        $categories_hashed = array();
        foreach ($categories as $category) {
            $categories_hashed[$category->getId()] = $category;
            $categories_count[$category->getId()] = 0;
        }
        foreach ($articles as $article)
            $categories_count[$article->getCategoryId()]++;
        $categories_hashed_copy = $categories_hashed;
        $categories_tree = $this->buildTree($categories_hashed_copy, $categories_count);

        // Articles
        $articles = array_reverse(array_slice($articles, -$max, $max));

        return array(
            "template" => __DIR__."/front_sidebar.html.twig",
            "params" => array(
                "categories" => $categories_tree,
                "articles" => $articles,
                "categories_hashed" => $categories_hashed
            )
        );
    }

    private function buildTree(array &$elements, array &$count, $parentId = 0)
    {
        $branch = array();

        foreach ($elements as $element) {
            if ($element->getParentId() == $parentId) {
                $id = $element->getId();
                $children = $this->buildTree($elements, $count, $id);

                $branch[$id]["object"] = $element;
                $branch[$id]["count"] = $count[$id];

                if ($children) {
                    $branch[$id]['children'] = $children;
                    foreach ($children as $child)
                        $branch[$id]["count"] += $child["count"];
                }

                unset($elements[$id]);
            }
        }

        return $branch;
    }
}
