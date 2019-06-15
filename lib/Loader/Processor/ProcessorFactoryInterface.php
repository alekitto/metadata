<?php declare(strict_types=1);

namespace Kcs\Metadata\Loader\Processor;

/**
 * Processor object factory.
 */
interface ProcessorFactoryInterface
{
    /**
     * Get a processor able to handle $subject.
     *
     * @param mixed $subject
     *
     * @return ProcessorInterface|null
     */
    public function getProcessor($subject): ?ProcessorInterface;
}
