<?php

use ModuleBundle\Util\ModuleInterface;

class Admin_Menu extends ModuleInterface
{
    public $name = "admin_menu";
    public $displayName = "Admin Navigation Menu";
    public $description = "Administrator panel navigation menu";
    public $pages = array(
        "admin_home",
        "admin_settings",
        "admin_modules",
        "admin_content",
    );

    public function install()
    {
        if (parent::install() &&
            $this->registerHook("displayNavigation"))
            return true;
        return false;
    }

    public function hook_displayNavigation()
    {
        $params = array();

        return array(
            "template" => __DIR__."/admin_menu.html.twig",
            "params" => $params
        );
    }
}
