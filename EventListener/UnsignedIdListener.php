<?php

namespace TeLiXj\PhpbbBundle\EventListener;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;

/**
 * Adds the unsigned option to all identifier fields of the bundle entities if MySQL is used as database platform.
 * This is necessary to use them to join with your entities and omit unsigned option in your mapping.
 */
class UnsignedIdListener extends AbstractListener
{
    protected function setMetadata(LoadClassMetadataEventArgs $args): void
    {
        if (!$args->getEntityManager()->getConnection()->getDatabasePlatform() instanceof MySQLPlatform) {
            return;
        }
        $classMetadata = $args->getClassMetadata();
        foreach ($classMetadata->fieldMappings as $field => $mapping) {
            if (in_array($field, $classMetadata->identifier)) {
                $mapping->options['unsigned'] = true;
            }
        }
    }
}
