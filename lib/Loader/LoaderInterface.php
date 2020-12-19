<?php

declare(strict_types=1);

namespace Kcs\Metadata\Loader;

use Kcs\Metadata\ClassMetadataInterface;

/**
 * Loads a {@see ClassMetadataInterface}.
 */
interface LoaderInterface
{
    /**
     * Populate class metadata.
     */
    public function loadClassMetadata(ClassMetadataInterface $classMetadata): bool;
}
