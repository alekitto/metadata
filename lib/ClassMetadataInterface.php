<?php declare(strict_types=1);

namespace Kcs\Metadata;

interface ClassMetadataInterface extends MetadataInterface
{
    /**
     * @return \ReflectionClass
     */
    public function getReflectionClass(): \ReflectionClass;

    /**
     * Returns a metadata instance for a given attribute.
     *
     * @param $name
     *
     * @return MetadataInterface
     */
    public function getAttributeMetadata(string $name): MetadataInterface;

    /**
     * Returns all attributes' metadata.
     *
     * @return MetadataInterface[]
     */
    public function getAttributesMetadata(): array;

    /**
     * Adds an attribute metadata.
     *
     * @param MetadataInterface $metadata
     */
    public function addAttributeMetadata(MetadataInterface $metadata): void;
}
