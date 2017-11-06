<?php

namespace ModuleBundle\Util;

use ModuleBundle\Entity\Module;
use HookBundle\Entity\HookModule;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class ModuleInterface
{
    /**
     * @var integer Module ID
     */
	public $id = NULL;

	/**
     * @var float Version
     */
	public $version;

	/**
     * @var string Unique name
     */
	public $name;

	/**
     * @var string Human name
     */
	public $displayName;

	/**
     * @var string A little description of the module
     */
	public $description;

	/**
     * @var boolean Status
     */
	public $active = false;

    /**
     * @var array Pages on which the module is loaded
     */
    public $pages = array();

    /**
     * @var array
     */
    public $_errors = array();

    /**
     * @var Module
     */
    protected $cached_entity;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var EntityManager em
     */
    protected $em;

    /**
     * @var ConfigurationManager config
     */
    protected $config;

    /**
     * @var string route_parameters
     */
    protected $route_params;
    protected $route;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->em = $container->get('doctrine.orm.entity_manager');
        $this->config = $container->get('config.manager');

        $request = $container->get('request_stack')->getCurrentRequest();
        $this->route_params = $request->get("_route_params");
        $this->route = $request->get('_route');

        if (is_null($this->version))
            $this->version = "1.0.0";

        if (is_null($this->name))
            return;

        $module = $this->em->getRepository("ModuleBundle:Module")->findOneByName($this->name);

        if (is_null($module))
            return;

        $this->id = $module->getId();
        $this->active = $module->isActive();
        $this->version = $module->getVersion();

        $this->cached_entity = $module;
    }

    public function install()
    {
        if (is_null($this->name)) {
            $_errors[] ="No name provided to the module! Please initiliaze the name.";
            return false;
        }

        if (!is_null($this->cached_entity))
            return;

        $module = new Module();

        $module->setName($this->name);
        $module->setActive(true);
        $module->setVersion($this->version);

        $this->em->persist($module);
        $this->em->flush();

        $this->cached_entity = $module;
        $this->id = $module->getId();

        return true;
    }

    public function uninstall()
    {
        if (is_null($this->name)) {
            $_errors[] = "Module not found!";
            return false;
        }

        // Remove hooks
        $hook_modules = $this->em->getRepository("HookBundle:HookModule")->findByIdModule($this->id);
        foreach ($hook_modules as $hook_module)
            $this->em->remove($hook_module);

        // Remove module
        $this->em->remove($this->cached_entity);
        $this->em->flush();

        return true;
    }

    public function registerHook($name)
    {
        $hook = $this->em->getRepository("HookBundle:Hook")->findOneByName($name);

        if (!$hook)
            return false;

        $hook_module = new HookModule();
        $hook_module->setIdModule($this->id);
        $hook_module->setIdHook($hook->getId());

        $this->em->persist($hook_module);
        $this->em->flush();

        return true;
    }

    public function unregisterHook($name)
    {
        $hook = $this->em->getRepository("HookBundle:Hook")->findOneByName($name);

        if (!$hook)
            return false;

        $hook_module = $this->em->getRepository("HookBundle:HookModule")->findOneBy(array(
            'idModule' => $this->id,
            'idHook' => $hook->getId()
        ));

        $this->em->remove($hook_module);
        $this->em->flush();
    }

    public function isInstalled()
    {
        return !is_null($this->id);
    }

    protected function redirectToRoute($route_name, $params)
    {
        $router = $this->container->get('router');
        $url = $router->generate($route_name, $params, UrlGeneratorInterface::ABSOLUTE_URL);

		header('Location: '.$url);
		exit;
    }
}
