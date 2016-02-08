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
     * @inheritDoc
     */
    public function __construct($class, $name)
    {
        $this->class = $class;
        $this->name = $name;

        $this->init();
    }

    public function getReflection()
    {
        return $this->reflectionProperty;
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
        return $this->getPublicPropertiesName();
    }

    /**
     * @inheritDoc
     */
    public function __wakeup()
    {
        $this->init();
    }

    private function init()
    {
        $this->reflectionProperty = new \ReflectionProperty($this->class, $this->name);
    }
}
