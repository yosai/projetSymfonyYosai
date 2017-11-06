<?php

namespace TemplateBundle\Service;

use Doctrine\ORM\EntityManager;
use TemplateBundle\Entity\Template;

class TemplateManager
{
    private $em;
    private $root;

    public function __construct(EntityManager $em, string $root)
    {
        $this->em = $em;
        $this->root = $root;
    }

    public function getTemplatePath()
    {
        $template = $this->em->getRepository("TemplateBundle:Template")->findOneByIsActive(true);

        return "@Template/" . $template->getName();
    }

    public function getAbsoluteTemplatePath()
    {
        $template = $this->em->getRepository("TemplateBundle:Template")->findOneByIsActive(true);

        return $this->root . "/templates/" . $template->getName();
    }

    public function getInstalled()
    {
        return $this->em->getRepository("TemplateBundle:Template")->findAll();
    }

    public function getCurrentTemplateName()
    {
        $template = $this->em->getRepository("TemplateBundle:Template")->findOneByIsActive(true);

        return $template->getName();
    }

    public function install($name, $active = false)
    {
        $template = new Template();
        $template->setName($name);
        $template->setActive($active);

        $this->em->persist($template);
        $this->em->flush();

        return $template;
    }

    public function change($name)
    {
        $template = $this->em->getRepository("TemplateBundle:Template")->findOneByName($name);

        // Test if template exists
        if ($template) {
            $templates = $this->getInstalled();
            foreach ($templates as $template)
                $this->set($template->getName(), $template->getName() == $name);
        }
    }

    public function uninstall($name)
    {
        $template = $this->em->getRepository("TemplateBundle:Template")->findOneByName($name);

        if ($template) {
            $this->em->remove($template);
            $this->em->flush();
        }
    }
}
