<?php

namespace Kcs\Metadata\Loader\Processor;

use Kcs\Metadata\MetadataInterface;

/**
 * Combine processors
 *
 * @internal
 */
class CompositeProcessor implements ProcessorInterface
{
    /**
     * @var array
     */
    private $processors;

    /**
     * Create a new instance containing specified processors
     *
     * @param ProcessorInterface[] $processors
     */
    public function __construct(array $processors)
    {
        $this->processors = $processors;
    }

    /**
     * {@inheritdoc}
     */
    public function process(MetadataInterface $metadata, $subject)
    {
        foreach ($this->processors as $processor) {
            $processor->process($metadata, $subject);
        }
    }
}
