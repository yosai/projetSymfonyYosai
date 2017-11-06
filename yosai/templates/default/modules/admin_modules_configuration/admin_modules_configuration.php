<?php

use ModuleBundle\Util\ModuleInterface;

class Admin_Modules_Configuration extends ModuleInterface
{
    public $name = "admin_modules_configuration";
    public $displayName = "Admin Modules Configuration";
    public $description = "Manage the configurations of modules from your administrator panel";
    public $pages = array(
        "admin_modules"
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
        // If no sub url request
        if (!array_key_exists("options", $this->route_params))
            return;

        $module_manager = $this->container->get('module.manager');

        // Check if action on module
        if ($this->route_params["options"] != "configuration") {
            $pattern = "/(\w+)-(configuration)/";
            preg_match_all($pattern, $this->route_params["options"], $matches, PREG_SET_ORDER);

            if (count($matches) == 0)
                return;

            $module = array(
                "name" => $matches[0][1],
                "action" => $matches[0][2]
            );

            return $module_manager->{$module["action"]}($module["name"]);
        }

        $modules = $module_manager->load("all");
        $params = array(
            "modules" => array()
        );

        foreach($modules as $key => $module)
            if (method_exists($module, "configuration"))
                $params["modules"][$key] = $module;

        return array(
            "template" => __DIR__."/admin_modules_configuration.html.twig",
            "params" => $params
        );
    }
}
