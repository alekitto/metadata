<?php

declare(strict_types=1);

namespace Kcs\Metadata\Factory;

use Kcs\Metadata\ClassMetadataInterface;
use Kcs\Metadata\Exception\InvalidArgumentException;

/**
 * Returns a {@see ClassMetadataInterface}.
 */
interface MetadataFactoryInterface
{
    /**
     * Returns a {@see ClassMetadataInterface}.
     * Note that if the method is called multiple times with the same class
     * name, the same metadata instance is returned.
     *
     * @phpstan-param object|class-string $value
     *
     * @throws InvalidArgumentException
     */
    public function getMetadataFor(object|string $value): ClassMetadataInterface;

    /**
     * Checks if class has metadata.
     *
     * @phpstan-param object|class-string $value
     */
    public function hasMetadataFor(object|string $value): bool;
}
