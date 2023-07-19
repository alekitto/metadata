<?php

declare(strict_types=1);

namespace Kcs\Metadata\Loader\Processor;

/**
 * Processor object factory.
 */
interface ProcessorFactoryInterface
{
    /**
     * Get a processor able to handle $subject.
     */
    public function getProcessor(object|string $subject): ProcessorInterface|null;
}
