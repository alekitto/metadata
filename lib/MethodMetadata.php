<?php

namespace Kcs\Metadata;

class MethodMetadata implements MetadataInterface
{
    use MetadataPropertiesTrait;

    /**
     * @var \ReflectionMethod
     */
    private $reflectionMethod;

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
        if (null === $this->reflectionMethod) {
            $this->reflectionMethod = new \ReflectionMethod($this->class, $this->name);
        }

        return $this->reflectionMethod;
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
