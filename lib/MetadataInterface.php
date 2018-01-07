<?php declare(strict_types=1);

namespace Kcs\Metadata;

interface MetadataInterface
{
    /**
     * Merge with another metadata instance
     * An {@see Exception\InvalidArgumentException} MUST be thrown if the
     * $metadata parameter is not mergeable.
     *
     * @param self $metadata
     *
     * @throws Exception\InvalidArgumentException
     */
    public function merge(self $metadata): void;

    /**
     * Get the name of the target class or attribute.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Returns a list of properties to be serialized.
     *
     * @return string[]
     */
    public function __sleep();
}
