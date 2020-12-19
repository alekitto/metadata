<?php

declare(strict_types=1);

namespace Kcs\Metadata;

use ReflectionClass;
use ReflectionProperty;

use function array_map;

trait MetadataPropertiesTrait
{
    /**
     * {@inheritdoc}
     */
    public function __sleep(): array
    {
        return $this->getPublicPropertiesName();
    }

    /**
     * {@inheritdoc}
     */
    public function __wakeup(): void
    {
    }

    /**
     * Get all the public properties' name.
     *
     * @return string[]
     */
    protected function getPublicPropertiesName(): array
    {
        $reflectionClass = new ReflectionClass($this);
        $publicProperties = $reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC);

        return array_map(static function (ReflectionProperty $property) {
            return $property->name;
        }, $publicProperties);
    }
}
