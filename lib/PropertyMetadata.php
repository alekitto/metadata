<?php

declare(strict_types=1);

namespace Kcs\Metadata;

use ReflectionProperty;

class PropertyMetadata implements MetadataInterface
{
    use AttributeMetadataTrait;
    use MetadataPropertiesTrait;

    private ReflectionProperty $reflectionProperty;

    /**
     * @phpstan-param class-string $class
     */
    public function __construct(string $class, string $name)
    {
        $this->class = $class;
        $this->name = $name;
    }

    public function getReflection(): ReflectionProperty
    {
        if (! isset($this->reflectionProperty)) {
            $this->reflectionProperty = new ReflectionProperty($this->class, $this->name);
        }

        return $this->reflectionProperty;
    }

    public function merge(MetadataInterface $metadata): void
    {
    }
}
