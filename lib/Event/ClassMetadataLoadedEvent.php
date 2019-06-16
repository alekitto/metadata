<?php declare(strict_types=1);

namespace Kcs\Metadata\Event;

use Kcs\Metadata\ClassMetadataInterface;
use Psr\EventDispatcher\StoppableEventInterface;

class ClassMetadataLoadedEvent implements StoppableEventInterface
{
    /**
     * @var bool
     */
    private $propagationStopped = false;

    /**
     * @var ClassMetadataInterface
     */
    private $metadata;

    public function __construct(ClassMetadataInterface $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * @return ClassMetadataInterface
     */
    public function getMetadata(): ClassMetadataInterface
    {
        return $this->metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    /**
     * Stops the propagation of the event to further event listeners.
     */
    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }
}
