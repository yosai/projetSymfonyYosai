<?php

use ModuleBundle\Util\ModuleInterface;

class Admin_Top_Bar extends ModuleInterface
{
    public $name = "admin_top_bar";
    public $displayName = "Admin Navigation Top Bar";
    public $description = "Administrator panel navigation top bar";
    public $pages = array(
        "admin_home",
        "admin_settings",
        "admin_modules",
        "admin_content",
    );

    public function install()
    {
        if (parent::install() &&
            $this->registerHook("displayContentTop"))
            return true;
        return false;
    }

    public function hook_displayContentTop()
    {
        $params = array();

        $params["current_user"] = $this->container->get('content.manager')->getUserManager()->getCurrent();

        return array(
            "template" => __DIR__."/admin_top_bar.html.twig",
            "params" => $params
        );
    }
}
