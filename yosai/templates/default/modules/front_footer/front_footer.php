<?php

use ModuleBundle\Util\ModuleInterface;

class Front_Footer extends ModuleInterface
{
    public $name = "front_footer";
    public $displayName = "Front Footer";
    public $description = "Front footer module";
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
            $this->registerHook("displayFooter"))
            return true;
        return false;
    }

    public function hook_displayFooter()
    {
        return array(
            "template" => __DIR__."/front_footer.html.twig",
            "params" => array(

            )
        );
    }
}
