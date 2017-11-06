<?php

use ModuleBundle\Util\ModuleInterface;

class Admin_Settings_General extends ModuleInterface
{
    public $name = "admin_settings_general";
    public $displayName = "Admin Settings General";
    public $description = "Administrator panel general settings manager page";
    public $pages = array(
        "admin_settings",
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
        if (!array_key_exists("options", $this->route_params) OR $this->route_params["options"] != "general")
            return;

        $config_manager = $this->container->get('config.manager');
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $maintenance = $config_manager->get('MODULE_SETTINGS_GENERAL_MAINTENANCE');

        if ($request->getMethod() == "POST") {
            $data = $request->request->all();

            // Maintenance mode
            $maintenance = array_key_exists("maintenance", $data)? true: false;
            $config_manager->update('MODULE_SETTINGS_GENERAL_MAINTENANCE', $maintenance);

            $this->redirectToRoute("admin_settings", array(
                "options" => "general"
            ));
        }

        $params = array(
            "maintenance" => $maintenance
        );

        return array(
            "template" => __DIR__."/admin_settings_general.html.twig",
            "params" => $params
        );
    }
}
