<?php

use ModuleBundle\Util\ModuleInterface;

class Front_Top_Bar extends ModuleInterface
{
    public $name = "front_top_bar";
    public $displayName = "Front Top Bar";
    public $description = "Front top bar";
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
        return array(
            "template" => __DIR__."/front_top_bar.html.twig",
            "params" => array(
                
            )
        );
    }
}
