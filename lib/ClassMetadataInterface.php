<?php

namespace Kcs\Metadata;

interface ClassMetadataInterface extends MetadataInterface
{
    /**
     * @return \ReflectionClass
     */
    public function getReflectionClass();

    /**
     * Returns a metadata instance for a given attribute
     *
     * @param $name
     *
     * @return MetadataInterface
     */
    public function getAttributeMetadata($name);

    /**
     * Returns all attributes' metadata
     *
     * @return MetadataInterface[]
     */
    public function getAttributesMetadata();

    /**
     * Adds an attribute metadata
     *
     * @param MetadataInterface $metadata
     */
    public function addAttributeMetadata(MetadataInterface $metadata);
}
