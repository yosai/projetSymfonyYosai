<?php

namespace ModuleBundle\Service;

use TemplateBundle\Service\TemplateManager;
use ConfigBundle\Service\ConfigurationManager;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class ModuleManager
{
    private $project_dir;
    private $project_modules_dir;
    private $template_modules_dir;
    private $container;
    private $em;

    public function __construct(Container $container, string $template_dir, string $project_dir)
    {
        $this->project_dir = $project_dir;
        $this->project_modules_dir = $project_dir . "/modules";
        $this->template_modules_dir = $template_dir . "/modules";
        $this->container = $container;
        $this->em = $container->get('doctrine.orm.entity_manager');
    }

    /**
     * Return sorted modules that need to be loaded
     *
     * @param string|array $modules
     * @return void
     */
    public function load($modules = "all")
    {
        // If all modules need to be loaded
        if ($modules == "all") {
            $template_class_modules = $this->loadTemplateModules($modules);
            $project_class_modules = $this->loadProjectModules($modules);

            return array_merge($template_class_modules, $project_class_modules);
        }

        $to_load = array();
        foreach($modules as $module)
            $to_load[$module[0]] = $module[1];

        $template_class_modules = $this->loadTemplateModules($to_load);
        $project_class_modules = $this->loadProjectModules($to_load);

        $loaded = array_merge($template_class_modules, $project_class_modules);
        $loaded_sort = array();
        foreach($modules as $key => $module)
            if (array_key_exists($module[0], $loaded))
                $loaded_sort[$key] = $loaded[$module[0]];

        ksort($loaded_sort);
        return $loaded_sort;
    }

    /**
     * Update the module to be active
     *
     * @param string $module_name
     * @return void
     */
    public function activate($module_name)
    {
        // @todo Make the activate method
        //       Find the module and update its value

        return true;
    }

    /**
     * Update the module to be inactive
     *
     * @param string $module_name
     * @return void
     */
    public function deactivate($module_name)
    {
        // @todo Make the deactivate method
        //       Find the module and update its value

        return true;
    }

    public function install($module_name)
    {
        $module = $this->em->getRepository("ModuleBundle:Module")->findOneByName($module_name);

        // Test if module is already installed
        if ($module)
            return true;

        $installed = NULL;
        $class_map = $this->createMap($this->template_modules_dir);
        // Search for the module
        foreach ($class_map as $class => $path) {
            if ($module_name == strtolower($class)) {
                // Load the module
                require_once $path;
                $module = new $class($this->container);

                // Install the module
                $installed = $module->install();
                break;
            }
        }

        return $installed;
    }

    public function uninstall($module_name)
    {
        $module = $this->em->getRepository("ModuleBundle:Module")->findOneByName($module_name);

        // Test if module is already uninstalled
        if (!$module)
            return true;

        $uninstalled = NULL;
        $class_map = $this->createMap($this->template_modules_dir);
        // Search for the module
        foreach ($class_map as $class => $path) {
            if ($module_name == strtolower($class)) {
                // Load the module
                require_once $path;
                $module = new $class($this->container);

                // Install the module
                $uninstalled = $module->uninstall();
                break;
            }
        }

        return $uninstalled;
    }

    public function configuration($module_name)
    {
        $module = $this->em->getRepository("ModuleBundle:Module")->findOneByName($module_name);

        // Test if module is installed and active
        if (!$module && !$module->isActive())
            return;

        $class_map = $this->createMap($this->template_modules_dir);
        // Search for the module
        foreach ($class_map as $class => $path) {
            if ($module_name == strtolower($class)) {
                // Load the module
                require_once $path;
                $module = new $class($this->container);

                if (!method_exists($module, "configuration"))
                    return;

                // Get the module configuration template
                return $module->configuration();
            }
        }
    }

    public function loadTemplateModules($modules)
    {
        $class_modules = array();
        $class_map = $this->createMap($this->template_modules_dir);

        foreach($class_map as $class => $path) {
            $module_name = strtolower($class);
            if ($modules != "all" && (!array_key_exists($module_name, $modules) || !$modules[$module_name]))
                continue;

            require_once $path;
            $module = new $class($this->container);

            if (!$module)
                throw new NotFoundHttpException("The module was not loaded correctly");

            $class_modules[$module_name] = $module;
        }

        return $class_modules;
    }

    public function loadProjectModules($modules)
    {
        $class_modules = array();
        $class_map = $this->createMap($this->project_modules_dir);

        foreach($class_map as $class => $path) {
            $module_name = strtolower($class);
            if ($modules != "all" && (!array_key_exists($module_name, $modules) || !$modules[$module_name]))
                continue;

            require_once $path;
            $module = new $class($this->em);

            if (!$module)
                throw new NotFoundHttpException("The module was not loaded correctly");

            $class_modules[$module->name] = $module;
        }

        return $class_modules;
    }

    /**
     * Iterate over all files in the given directory searching for classes.
     *
     * @param \Iterator|string $dir The directory to search in or an iterator
     *
     * @return array A class map array
     */
    private function createMap($dir)
    {
        if (is_string($dir)) {
            $dir = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
        }

        $map = array();

        foreach ($dir as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $path = $file->getRealPath() ?: $file->getPathname();

            if (pathinfo($path, PATHINFO_EXTENSION) !== 'php') {
                continue;
            }

            $classes = $this->findClasses($path);

            if (\PHP_VERSION_ID >= 70000) {
                // PHP 7 memory manager will not release after token_get_all(), see https://bugs.php.net/70098
                gc_mem_caches();
            }

            foreach ($classes as $class) {
                $map[$class] = $path;
            }
        }

        return $map;
    }

    /**
     * Extract the classes in the given file.
     *
     * @param string $path The file to check
     *
     * @return array The found classes
     */
    private static function findClasses($path)
    {
        $contents = file_get_contents($path);
        $tokens = token_get_all($contents);

        $classes = array();

        $namespace = '';
        for ($i = 0; isset($tokens[$i]); ++$i) {
            $token = $tokens[$i];

            if (!isset($token[1])) {
                continue;
            }

            $class = '';

            switch ($token[0]) {
                case T_NAMESPACE:
                    $namespace = '';
                    // If there is a namespace, extract it
                    while (isset($tokens[++$i][1])) {
                        if (in_array($tokens[$i][0], array(T_STRING, T_NS_SEPARATOR))) {
                            $namespace .= $tokens[$i][1];
                        }
                    }
                    $namespace .= '\\';
                    break;
                case T_CLASS:
                case T_INTERFACE:
                case T_TRAIT:
                    // Skip usage of ::class constant
                    $isClassConstant = false;
                    for ($j = $i - 1; $j > 0; --$j) {
                        if (!isset($tokens[$j][1])) {
                            break;
                        }

                        if (T_DOUBLE_COLON === $tokens[$j][0]) {
                            $isClassConstant = true;
                            break;
                        } elseif (!in_array($tokens[$j][0], array(T_WHITESPACE, T_DOC_COMMENT, T_COMMENT))) {
                            break;
                        }
                    }

                    if ($isClassConstant) {
                        break;
                    }

                    // Find the classname
                    while (isset($tokens[++$i][1])) {
                        $t = $tokens[$i];
                        if (T_STRING === $t[0]) {
                            $class .= $t[1];
                        } elseif ('' !== $class && T_WHITESPACE === $t[0]) {
                            break;
                        }
                    }

                    $classes[] = ltrim($namespace.$class, '\\');
                    break;
                default:
                    break;
            }
        }

        return $classes;
    }
}
