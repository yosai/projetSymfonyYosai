<?php

namespace HookBundle\Twig;

use HookBundle\Entity\Hook;
use HookBundle\Entity\HookModule;
use HookBundle\HookBundle;
use ModuleBundle\Entity\Module;
use TemplateBundle\Service\TemplateManager;
use Twig_Extension;
use Twig_Environment;
use Twig_SimpleFunction;
use Doctrine\ORM\EntityManager;
use ModuleBundle\Service\ModuleManager;

class HookExtension extends Twig_Extension
{
    private $em;
    private $mm;

    // @todo Inject the entity manager from the services.yml file
    public function __construct(EntityManager $em, ModuleManager $mm)
    {
        $this->em = $em;
        $this->mm = $mm;
    }

    public function getFunctions()
    {
        return array(
            new Twig_SimpleFunction('hook', array($this, 'hookFunction'), array(
                "is_safe" => array("html"),     // Allow HTML to be returned and displayed
                'needs_environment' => true,    // Inject the Twig environment into the function
            )),
        );
    }

    /**
     * Hook from template
     * @param  Twig_Environment $env       Twig service for rendering
     * @param  String           $hook_name
     * @param  String           $page_name
     * @return String                      The HTML to display
     */
    public function hookFunction(Twig_Environment $env, $hook_name, $page_name)
    {// $logger->info('I just got the logger');
        // @todo Make the hook function
        //       1. Load hook from DB

        $hook =  $this->em->getRepository('HookBundle:Hook')->findOneBy( array('name' => $hook_name));
        $modulesHook = $this->em->getRepository('HookBundle:HookModule')->findBy(array('idHook' => $hook->getId()),array('position' => 'ASC'));
        $modules = $this->em->getRepository('ModuleBundle:Module')->findBy(['id' => $modulesHook]);

        $html=$hook_name.' - test'; // pour tester


        $arrayModuleNames = array();
        foreach ($modules as $module) {
            $arrayModuleNames[$module->getName()]=$module;
        }

        dump($arrayModuleNames);
       // $loadedModules = $this->mm->load($modules);



        return $html;




        //       2. Load modules ID registered with the hook
        //       3. Load modules
        //       4. Sort them using hook modules position
        //       5. Render each module view
        //       6. Return HTML

              //  return $view;
    }


    public function getName()
    {
        return "hook_extension";
    }
}
