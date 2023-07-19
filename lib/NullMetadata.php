<?php

declare(strict_types=1);

namespace Kcs\Metadata;

/**
 * Represents undefined or empty metadata class.
 */
final class NullMetadata implements MetadataInterface
{
    public function __construct(public string $name)
    {
    }

    public function merge(MetadataInterface $metadata): void
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function __sleep(): array
    {
        return [];
    }

    public function __wakeup(): void
    {
    }
}
