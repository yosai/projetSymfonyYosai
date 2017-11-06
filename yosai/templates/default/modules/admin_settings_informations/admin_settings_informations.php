<?php

use ModuleBundle\Util\ModuleInterface;

class Admin_Settings_Informations extends ModuleInterface
{
    public $name = "admin_settings_informations";
    public $displayName = "Admin Settings Informations";
    public $description = "Administrator panel informations settings manager page";
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
        if (!array_key_exists("options", $this->route_params) OR $this->route_params["options"] != "informations")
            return;

        require_once $this->container->getParameter("kernel.project_dir") . '/var/SymfonyRequirements.php';
        $symfonyRequirements = new SymfonyRequirements();
        $pdo = $this->em->getConnection();

        $params = array();
        $params["php_uname"] = php_uname();
        $params["php_version"] = phpversion();
        $params["php_memory_limit"] = ini_get('memory_limit');
        $params["php_max_execution_time"] = ini_get('max_execution_time');
        $params["php_post_max_size"] = ini_get('post_max_size');
        $params["mysql_version"] = $pdo->query('select version()')->fetchColumn();
        $params["mysql_server"] = $this->container->getParameter("database_host");
        $params["mysql_name"] = $this->container->getParameter("database_name");
        $params["mysql_user"] = $this->container->getParameter("database_user");
        $params["mysql_prefix"] = $this->container->getParameter("database_prefix");
        $params["mysql_engine"] = "InnoDB";
        $params["mysql_driver"] = "DbPDO";
        $params["required"] = $symfonyRequirements->getFailedRequirements();
        $params["optional"] = $symfonyRequirements->getFailedRecommendations();

        return array(
            "template" => __DIR__."/admin_settings_informations.html.twig",
            "params" => $params
        );
    }
}
