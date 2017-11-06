<?php

use ModuleBundle\Util\ModuleInterface;

class Admin_Modules_Manager extends ModuleInterface
{
    public $name = "admin_modules_manager";
    public $displayName = "Admin Modules Manager";
    public $description = "Manage the installation and uninstallation of modules from your administrator panel";
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
        if ($this->route_params["options"] != "all") {
            $pattern = "/(\w+)-(uninstall|install|activate|deactivate)/";
            preg_match_all($pattern, $this->route_params["options"], $matches, PREG_SET_ORDER);

            if (count($matches) == 0)
                return;

            $module = array(
                "name" => $matches[0][1],
                "action" => $matches[0][2]
            );

            $module_manager->{$module["action"]}($module["name"]);

            $this->redirectToRoute("admin_modules", array(
                "options" => "all"
            ));
        }

        $params = array();
        $params["template_modules"] = $module_manager->loadTemplateModules("all");
        $params["project_modules"] = $module_manager->loadProjectModules("all");

        return array(
            "template" => __DIR__."/admin_modules_manager.html.twig",
            "params" => $params
        );
    }
}
