<?php

namespace Kcs\Metadata;

/**
 * Represents undefined or empty metadata class
 */
final class NullMetadata implements MetadataInterface
{
    public $name;

    /**
     * {@inheritdoc}
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(MetadataInterface $metadata)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function __sleep()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function __wakeup()
    {
    }
}
