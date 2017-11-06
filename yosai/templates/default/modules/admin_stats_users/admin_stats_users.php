<?php

use ModuleBundle\Util\ModuleInterface;

class Admin_Stats_Users extends ModuleInterface
{
    public $name = "admin_stats_users";
    public $displayName = "Admin Homepage Users Statistics";
    public $description = "Administrator panel users statistics";
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
        $user_manager = $this->container->get('content.manager')->getUserManager();

        $params = array();
        $params["users"] = $user_manager->get();

        return array(
            "template" => __DIR__."/admin_stats_users.html.twig",
            "params" => $params
        );
    }
}
