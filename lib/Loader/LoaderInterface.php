<?php

namespace Kcs\Metadata\Loader;

use Kcs\Metadata\ClassMetadataInterface;

/**
 * Loads a {@see ClassMetadataInterface}
 *
 * @author Alessandro Chitolina <alekitto@gmail.com>
 */
interface LoaderInterface
{
    /**
     * Populate class metadata
     *
     * @param ClassMetadataInterface $classMetadata
     *
     * @return bool
     */
    public function loadClassMetadata(ClassMetadataInterface $classMetadata);
}
