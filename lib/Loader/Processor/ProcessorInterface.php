<?php

declare(strict_types=1);

namespace Kcs\Metadata\Loader\Processor;

use Kcs\Metadata\MetadataInterface;

/**
 * Metadata representation processor.
 */
interface ProcessorInterface
{
    /**
     * Load metadata from subject.
     */
    public function process(MetadataInterface $metadata, mixed $subject): void;
}
