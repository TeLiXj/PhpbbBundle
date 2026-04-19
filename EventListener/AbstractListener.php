<?php

namespace TeLiXj\PhpbbBundle\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use TeLiXj\PhpbbBundle\Entity;

/**
 * Base class for listeners that need to set custom metadata for bundle entities
 */
abstract class AbstractListener
{
    public function __construct(protected string $prefix) {}

    public function __invoke(LoadClassMetadataEventArgs $args): void
    {
        if (str_starts_with($args->getClassMetadata()->name, Entity::class)) {
            $this->setMetadata($args);
        }
    }

    abstract protected function setMetadata(LoadClassMetadataEventArgs $args): void;
}
