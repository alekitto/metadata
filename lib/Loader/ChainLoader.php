<?php

declare(strict_types=1);

namespace Kcs\Metadata\Loader;

use InvalidArgumentException;
use Kcs\Metadata\ClassMetadataInterface;

use function sprintf;

class ChainLoader implements LoaderInterface
{
    /** @param LoaderInterface[] $loaders */
    public function __construct(private array $loaders)
    {
        foreach ($loaders as $loader) {
            if (! $loader instanceof LoaderInterface) {
                throw new InvalidArgumentException(sprintf('Class %s is expected to implement LoaderInterface', $loader::class));
            }
        }
    }

    public function loadClassMetadata(ClassMetadataInterface $classMetadata): bool
    {
        $success = false;

        foreach ($this->loaders as $loader) {
            $success = $loader->loadClassMetadata($classMetadata) || $success;
        }

        return $success;
    }
}
