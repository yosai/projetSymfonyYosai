<?php

namespace ModuleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Module
 *
 * @ORM\Table(name="module")
 * @ORM\Entity(repositoryClass="ModuleBundle\Repository\ModuleRepository")
 */
class Module
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @var string
     *
     * @ORM\Column(name="version", type="string", length=15)
     */
    private $version;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Module
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Module
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Set version
     *
     * @param string $version
     *
     * @return Module
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }
}
