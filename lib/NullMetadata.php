<?php declare(strict_types=1);

namespace Kcs\Metadata;

/**
 * Represents undefined or empty metadata class.
 */
final class NullMetadata implements MetadataInterface
{
    public $name;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(MetadataInterface $metadata): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
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
