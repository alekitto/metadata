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
     *
     * @param mixed $subject
     */
    public function process(MetadataInterface $metadata, $subject): void;
}
