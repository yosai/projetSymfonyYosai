<?php

namespace ConfigBundle\Subscriber;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\Common\EventSubscriber;

class TablePrefixSubscriber implements EventSubscriber
{
    protected $prefix = '';

    public function __construct($prefix)
    {
        $this->prefix = (string) $prefix;
    }

    public function getSubscribedEvents()
    {
        return array('loadClassMetadata');
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $args)
    {
        $classMetadata = $args->getClassMetadata();
        if ($classMetadata->isInheritanceTypeSingleTable() && !$classMetadata->isRootEntity())
            return; // if we are in an inheritance hierarchy, only apply this once

        $classMetadata->setTableName($this->prefix . $classMetadata->getTableName());

        foreach ($classMetadata->getAssociationMappings() as $fieldName => $mapping)
        {
            // Check if "joinTable" exists, it can be null if this field is the reverse side of a ManyToMany relationship
            if ($mapping['type'] == ClassMetadataInfo::MANY_TO_MANY &&
                array_key_exists('name', $classMetadata->associationMappings[$fieldName]['joinTable']))
            {
                $mappedTableName = $classMetadata->associationMappings[$fieldName]['joinTable']['name'];
                $classMetadata->associationMappings[$fieldName]['joinTable']['name'] = $this->prefix . $mappedTableName;
            }
        }
    }
}
