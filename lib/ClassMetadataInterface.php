<?php

declare(strict_types=1);

namespace Kcs\Metadata;

use ReflectionClass;

interface ClassMetadataInterface extends MetadataInterface
{
    public function getReflectionClass(): ReflectionClass;

    /**
     * Returns a metadata instance for a given attribute.
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
     */
    public function addAttributeMetadata(MetadataInterface $metadata): void;

    /**
     * Called after all attributes metadata has been loaded and this metadata instance
     * is merged with the parents.
     */
     public function finalize(): void;
}
