<?php declare(strict_types=1);

namespace Kcs\Metadata;

trait MetadataPropertiesTrait
{
    /**
     * {@inheritdoc}
     */
    public function __sleep()
    {
        return $this->getPublicPropertiesName();
    }

    /**
     * {@inheritdoc}
     */
    public function __wakeup()
    {
    }

    /**
     * Get all the public properties' name.
     *
     * @return array
     */
    protected function getPublicPropertiesName(): array
    {
        $reflectionClass = new \ReflectionClass($this);
        $publicProperties = $reflectionClass->getProperties(\ReflectionProperty::IS_PUBLIC);

        return array_map(function (\ReflectionProperty $property) {
            return $property->name;
        }, $publicProperties);
    }
}
