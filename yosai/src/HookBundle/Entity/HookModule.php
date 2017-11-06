<?php

namespace HookBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * HookModule
 *
 * @ORM\Table(name="hook_module")
 * @ORM\Entity(repositoryClass="HookBundle\Repository\HookModuleRepository")
 */
class HookModule
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
     * @var int
     *
     * @ORM\Column(name="id_module", type="integer")
     */
    private $idModule;

    /**
     * @var int
     *
     * @ORM\Column(name="id_hook", type="integer")
     */
    private $idHook;

    /**
     * @var int
     *
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer")
     */
    private $position;


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
     * Set idModule
     *
     * @param integer $idModule
     *
     * @return HookModule
     */
    public function setIdModule($idModule)
    {
        $this->idModule = $idModule;

        return $this;
    }

    /**
     * Get idModule
     *
     * @return int
     */
    public function getIdModule()
    {
        return $this->idModule;
    }

    /**
     * Set idHook
     *
     * @param integer $idHook
     *
     * @return HookModule
     */
    public function setIdHook($idHook)
    {
        $this->idHook = $idHook;

        return $this;
    }

    /**
     * Get idHook
     *
     * @return int
     */
    public function getIdHook()
    {
        return $this->idHook;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return HookModule
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }
}
