<?php declare(strict_types=1);

namespace Kcs\Metadata\Event;

use Kcs\Metadata\ClassMetadataInterface;
use Symfony\Contracts\EventDispatcher\Event;

class ClassMetadataLoadedEvent extends Event
{
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
}
