<?php

declare(strict_types=1);

namespace Kcs\Metadata\Loader;

use InvalidArgumentException;
use Kcs\Metadata\ClassMetadataInterface;

use function get_class;
use function Safe\sprintf;

class ChainLoader implements LoaderInterface
{
    /** @var LoaderInterface[] */
    private array $loaders;

    /**
     * @param LoaderInterface[] $loaders
     */
    public function __construct(array $loaders)
    {
        foreach ($loaders as $loader) {
            if (! $loader instanceof LoaderInterface) {
                throw new InvalidArgumentException(sprintf('Class %s is expected to implement LoaderInterface', get_class($loader)));
            }
        }

        $this->loaders = $loaders;
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
