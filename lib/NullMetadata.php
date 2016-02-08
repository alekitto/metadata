<?php

namespace Kcs\Metadata;

/**
 * Represents undefined or empty metadata class
 */
final class NullMetadata implements MetadataInterface
{
    public $name;

    /**
     * @inheritDoc
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @inheritDoc
     */
    public function merge(MetadataInterface $metadata)
    {
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function __sleep()
    {
        return array ();
    }
}
