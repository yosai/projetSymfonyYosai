<?php

use ModuleBundle\Util\ModuleInterface;
use Symfony\Component\Finder\Finder;

class Admin_Settings_Templates extends ModuleInterface
{
    public $name = "admin_settings_templates";
    public $displayName = "Admin Settings Templates";
    public $description = "Administrator panel templates manager page";
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
        // START page matching
        if (!array_key_exists("options", $this->route_params))
            return;

        $pattern = "/template(-(\w+)-install)?/";
        preg_match_all($pattern, $this->route_params["options"], $matches, PREG_SET_ORDER);

        if (count($matches) == 0)
            return;
        // END page matching

        $template_manager = $this->container->get('template.manager');
        $templates = $template_manager->getInstalled();

        // Install template
        if (count($matches[0]) == 3) {
            $template_name = $matches[0][2];
            $template_manager->change($template_name);

            $this->redirectToRoute("admin_settings", array(
                "options" => "template"
            ));
        }

        $finder = new Finder();

        // List templates
        $names = array_map(function($template) {
            return $template->getName();
        }, $templates);

        // Get all templates
        $finder->directories()->depth('== 0')->in($this->container->getParameter('kernel.project_dir') . "/templates");
        $directories = array();
        foreach ($finder as $directory)
            $directories[] = $directory->getFilename();

        $diff_dir = array_diff($directories, $names);
        $diff_db = array_diff($names, $directories);

        foreach($diff_dir as $diff)
            $template_manager->install($diff);
        foreach($diff_db as $diff)
            $template_manager->uninstall($diff);

        return array(
            "template" => __DIR__."/admin_settings_templates.html.twig",
            "params" => array(
                "templates" => $template_manager->getInstalled()
            )
        );
    }
}
