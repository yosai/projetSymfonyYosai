<?php

use ModuleBundle\Util\ModuleInterface;

class Front_Nav_Bar extends ModuleInterface
{
    public $name = "front_nav_bar";
    public $displayName = "Front Navigation Bar";
    public $description = "Front navigation menu bar";
    public $pages = array(
        "front_home",
        "front_category",
        "front_article",
        "front_search",
        "front_login"
    );

    public function install()
    {
        if (parent::install() &&
            $this->registerHook("displayHeader"))
            return true;
        return false;
    }

    public function hook_displayHeader()
    {
        $categories = $this->container->get('content.manager')->getCategoryManager()->get();
        $categories_tree = array();

        foreach ($categories as $category) {
            if (is_null($category->getParentId())) {
                $categories_tree[$category->getId()] = array(
                    "name" => $category->getName(),
                    "slug" => $category->getSlug(),
                    "children" => array()
                );
            }
        }

        foreach ($categories as $category) {
            if (!is_null($category->getParentId()) AND
                array_key_exists($category->getParentId(), $categories_tree)
            ) {
                $categories_tree[$category->getParentId()]["children"][] = $category;
            }
        }

        return array(
            "template" => __DIR__."/front_nav_bar.html.twig",
            "params" => array(
                "categories" => $categories_tree
            )
        );
    }
}
