<?php

declare(strict_types=1);

namespace Kcs\Metadata\Loader\Processor;

use Kcs\Metadata\MetadataInterface;

/**
 * Combine processors.
 *
 * @internal
 */
class CompositeProcessor implements ProcessorInterface
{
    /**
     * Create a new instance containing specified processors.
     *
     * @param ProcessorInterface[] $processors
     */
    public function __construct(private array $processors)
    {
    }

    public function process(MetadataInterface $metadata, mixed $subject): void
    {
        foreach ($this->processors as $processor) {
            $processor->process($metadata, $subject);
        }
    }
}
