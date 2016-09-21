<?php

namespace Kcs\Metadata;

class PropertyMetadata implements MetadataInterface
{
    use MetadataPropertiesTrait;

    /**
     * @var \ReflectionProperty
     */
    private $reflectionProperty;

    /**
     * @var string
     */
    public $class;

    /**
     * @var string
     */
    public $name;

    /**
     * {@inheritdoc}
     */
    public function __construct($class, $name)
    {
        $this->class = $class;
        $this->name = $name;
    }

    public function getReflection()
    {
        if (null === $this->reflectionProperty) {
            $this->reflectionProperty = new \ReflectionProperty($this->class, $this->name);
        }

        return $this->reflectionProperty;
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
        return $this->getPublicPropertiesName();
    }

    /**
     * {@inheritdoc}
     */
    public function __wakeup()
    {
    }
}
