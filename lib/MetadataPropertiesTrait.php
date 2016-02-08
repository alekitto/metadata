<?php

namespace Kcs\Metadata;

trait MetadataPropertiesTrait
{
    /**
     * Get all the public properties' name
     *
     * @return array
     */
    protected function getPublicPropertiesName()
    {
        $reflectionClass = new \ReflectionClass($this);
        $publicProperties = $reflectionClass->getProperties(\ReflectionProperty::IS_PUBLIC);

        return array_map(function (\ReflectionProperty $property) {
            return $property->name;
        }, $publicProperties);
    }
}
