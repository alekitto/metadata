<?php

namespace Kcs\Metadata\Factory;

use Kcs\Metadata\ClassMetadata;

class MetadataFactory extends AbstractMetadataFactory
{
    /**
     * {@inheritdoc}
     */
    protected function createMetadata(\ReflectionClass $class)
    {
        return new ClassMetadata($class);
    }
}
