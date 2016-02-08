<?php

namespace Kcs\Metadata\Event;

use Kcs\Metadata\ClassMetadataInterface;
use Symfony\Component\EventDispatcher\Event;

class ClassMetadataLoadedEvent extends Event
{
    const LOADED_EVENT = 'kcs_metadata.metadata_loaded';

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
    public function getMetadata()
    {
        return $this->metadata;
    }
}
