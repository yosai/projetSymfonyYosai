<?php

namespace TemplateBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Finder\Finder;
use AppKernel;

class AssetManager
{
    private $local_stylesheets = "@TemplateBundle/Resources/public/css";
    private $local_javascripts = "@TemplateBundle/Resources/public/js";

    private $kernel;
    private $template_manager;
    private $route_name;

    public function __construct(AppKernel $kernel, TemplateManager $template_manager, RequestStack $request)
    {
        $this->kernel = $kernel;
        $this->template_manager = $template_manager;
        $this->route_name = $request->getCurrentRequest()->get('_route');
    }

    /**
     * Return the path to the active template stylesheets
     *
     * @return string
     */
    public function getTemplateStylesheetsPath()
    {
        if (strpos($this->route_name, "front") === 0)
            $path = $this->template_manager->getAbsoluteTemplatePath() . "/front/css";
        else if (strpos($this->route_name, "admin") === 0)
            $path = $this->template_manager->getAbsoluteTemplatePath() . "/admin/css";

        return $path;
    }

    /**
     * Return all stylesheets from the bundle
     *
     * @return array
     */
    private function getLocalStylesheets()
    {
        // @todo Find local stylesheets and return them
        // @tips Use the Finder class
    }

    /**
     * Return all stylesheets from the active template
     *
     * @return array
     */
    private function getTemplateStylesheets()
    {
        // @todo Find template stylesheets and return them
        // @tips Use the Finder class
    }

    public function getStylesheets()
    {
        return array_merge(
            $this->getLocalStylesheets(),
            $this->getTemplateStylesheets()
        );
    }

    public function getTemplateJavascriptsPath()
    {
        if (strpos($this->route_name, "front") === 0)
            $path = $this->template_manager->getAbsoluteTemplatePath() . "/front/js";
        else if (strpos($this->route_name, "admin") === 0)
            $path = $this->template_manager->getAbsoluteTemplatePath() . "/admin/js";

        return $path;
    }

    /**
     * Return all javascripts from the bundle
     *
     * @return array
     */
    public function getLocalJavascripts()
    {
        // @todo Find local javascripts and return them
        // @tips Use the Finder class
    }

    /**
     * Return all javascripts from the active template
     *
     * @return array
     */
    private function getTemplateJavascripts()
    {
        // @todo Find template javascripts and return them
        // @tips Use the Finder class
    }

    public function getJavascripts()
    {
        return array_merge(
            $this->getLocalJavascripts(),
            $this->getTemplateJavascripts()
        );
    }
}
