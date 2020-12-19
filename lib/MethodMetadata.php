<?php

declare(strict_types=1);

namespace Kcs\Metadata;

use ReflectionMethod;

class MethodMetadata implements MetadataInterface
{
    use AttributeMetadataTrait;
    use MetadataPropertiesTrait;

    private ReflectionMethod $reflectionMethod;

    public function __construct(string $class, string $name)
    {
        $this->class = $class;
        $this->name = $name;
    }

    public function getReflection(): ReflectionMethod
    {
        if (! isset($this->reflectionMethod)) {
            $this->reflectionMethod = new ReflectionMethod($this->class, $this->name);
        }

        return $this->reflectionMethod;
    }

    public function merge(MetadataInterface $metadata): void
    {
    }
}
