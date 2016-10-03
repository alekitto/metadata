<?php

namespace Kcs\Metadata\Factory;

use Kcs\Metadata\Exception\InvalidArgumentException;

class MetadataFactory extends AbstractMetadataFactory
{
    /**
     * Metadata object to be created
     *
     * @var string
     */
    private $metadataClass = 'Kcs\Metadata\ClassMetadata';

    /**
     * Set the metadata class to be created by this factory
     *
     * @param string $metadataClass
     */
    public function setMetadataClass($metadataClass)
    {
        if (! class_exists($metadataClass) || ! is_subclass_of($metadataClass, 'Kcs\Metadata\ClassMetadataInterface', true)) {
            throw InvalidArgumentException::create(InvalidArgumentException::INVALID_METADATA_CLASS, $metadataClass);
        }

        $this->metadataClass = $metadataClass;
    }

    /**
     * {@inheritdoc}
     */
    protected function createMetadata(\ReflectionClass $class)
    {
        return new $this->metadataClass($class);
    }
}
