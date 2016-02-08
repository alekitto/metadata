<?php

namespace Kcs\Metadata\Factory;
use Kcs\Metadata\ClassMetadataInterface;
use Kcs\Metadata\Exception\InvalidArgumentException;

/**
 * Returns a {@see ClassMetadataInterface}
 *
 * @author Alessandro Chitolina <alekitto@gmail.com>
 */
interface MetadataFactoryInterface
{
    /**
     * Returns a {@see ClassMetadataInterface}.
     * Note that if the method is called multiple times with the same class
     * name, the same metadata instance is returned.
     *
     * @param object|string $value
     *
     * @return ClassMetadataInterface
     *
     * @throws InvalidArgumentException
     */
    public function getMetadataFor($value);

    /**
     * Checks if class has metadata
     *
     * @param object|string $value
     *
     * @return bool
     */
    public function hasMetadataFor($value);
}
