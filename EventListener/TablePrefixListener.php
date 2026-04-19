<?php

namespace TeLiXj\PhpbbBundle\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * The table prefix for the users table is defined within the configuration for this bundle.
 * This listener makes sure the table name is.
 */
class TablePrefixListener extends AbstractListener
{
    protected function setMetadata(LoadClassMetadataEventArgs $args): void
    {
        $classMetadata = $args->getClassMetadata();
        if (!$classMetadata->isInheritanceTypeSingleTable() || $classMetadata->getName() === $classMetadata->rootEntityName) {
            $classMetadata->setPrimaryTable(['name' => $this->prefix.$classMetadata->getTableName()]);
        }
        foreach ($classMetadata->getAssociationMappings() as $fieldName => $mapping) {
            if (ClassMetadata::MANY_TO_MANY == $mapping['type'] && true == $mapping['isOwningSide']) {
                $mappedTableName = $mapping['joinTable']['name'];
                $classMetadata->associationMappings[$fieldName]['joinTable']['name'] = $this->prefix.$mappedTableName;
            }
        }
    }
}
