<?php

namespace Kcs\Metadata;

interface MetadataInterface
{
    /**
     * Merge with another metadata instance
     * An {@see Exception\InvalidArgumentException} MUST be thrown if the
     * $metadata parameter is not mergeable.
     *
     * @param MetadataInterface $metadata
     *
     * @throws Exception\InvalidArgumentException
     */
    public function merge(MetadataInterface $metadata);

    /**
     * Get the name of the target class or attribute
     *
     * @return string
     */
    public function getName();

    /**
     * Returns a list of properties to be serialized
     *
     * @return string[]
     */
    public function __sleep();
}
