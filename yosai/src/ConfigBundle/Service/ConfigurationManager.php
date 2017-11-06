<?php

namespace ConfigBundle\Service;

use Doctrine\ORM\EntityManager;
use ConfigBundle\Entity\Configuration;

class ConfigurationManager
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function get(string $name)
    {
        $config = $this->em->getRepository("ConfigBundle:Configuration")->findOneByName($name);

        return $config? unserialize($config->getValue()): NULL;
    }

    public function create(string $name, string $value)
    {
        $configuration = new Configuration();
        $configuration->setName($name);
        $configuration->setValue(serialize($value));

        $this->em->persist($configuration);
        $this->em->flush();

        return true;
    }

    public function update(string $name, string $value)
    {
        $configuration = $this->em->getRepository("ConfigBundle:Configuration")->findOneByName($name);

        if(!$configuration)
            $this->create($name, $value);
        else
            $configuration->setValue(serialize($value));

        $this->em->flush();

        return true;
    }
}
