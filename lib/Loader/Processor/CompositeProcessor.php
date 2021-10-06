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
    /** @var ProcessorInterface[] */
    private array $processors;

    /**
     * Create a new instance containing specified processors.
     *
     * @param ProcessorInterface[] $processors
     */
    public function __construct(array $processors)
    {
        $this->processors = $processors;
    }

    public function process(MetadataInterface $metadata, mixed $subject): void
    {
        foreach ($this->processors as $processor) {
            $processor->process($metadata, $subject);
        }
    }
}
