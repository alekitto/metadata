<?php

namespace Kcs\Metadata\Loader\Processor;

use Kcs\Metadata\MetadataInterface;

/**
 * Metadata representation processor
 */
interface ProcessorInterface
{
    /**
     * Load metadata from subject
     *
     * @param MetadataInterface $metadata
     * @param mixed $subject
     */
    public function process(MetadataInterface $metadata, $subject);
}
