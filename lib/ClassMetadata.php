<?php

namespace Kcs\Metadata;

use Kcs\Metadata\Exception\InvalidArgumentException;

class ClassMetadata implements ClassMetadataInterface
{
    use MetadataPropertiesTrait;

    /**
     * @var \ReflectionClass
     */
    private $reflectionClass;

    /**
     * @var string
     */
    public $name;

    /**
     * @var MetadataInterface[]
     */
    public $attributesMetadata;

    public function __construct(\ReflectionClass $class)
    {
        $this->reflectionClass = $class;
        $this->name = $class->name;
        $this->attributesMetadata = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getReflectionClass()
    {
        return $this->reflectionClass;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(MetadataInterface $metadata)
    {
        if ($metadata instanceof NullMetadata) {
            return;
        }

        if (! $metadata instanceof ClassMetadataInterface) {
            throw InvalidArgumentException::create(InvalidArgumentException::NOT_MERGEABLE_METADATA, $this, $metadata);
        }

        if (! $this->getReflectionClass()->isSubclassOf($metadata->getReflectionClass()->name)) {
            throw InvalidArgumentException::create(
                InvalidArgumentException::NOT_MERGEABLE_METADATA,
                $this->getReflectionClass()->name,
                $metadata->getReflectionClass()->name
            );
        }

        $otherAttributes = $metadata->getAttributesMetadata();
        foreach ($otherAttributes as $attrName => $attrMetadata) {
            $target = $this->getAttributeMetadata($attrName);
            if ($target instanceof NullMetadata) {
                $this->attributesMetadata[$attrName] = $attrMetadata;
                continue;
            }

            $target->merge($attrMetadata);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeMetadata($name)
    {
        if (! isset($this->attributesMetadata[$name])) {
            return new NullMetadata($name);
        }

        return $this->attributesMetadata[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributesMetadata()
    {
        return $this->attributesMetadata;
    }

    /**
     * {@inheritdoc}
     */
    public function addAttributeMetadata(MetadataInterface $metadata)
    {
        $this->attributesMetadata[ $metadata->getName() ] = $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getReflectionClass()->name;
    }

    /**
     * {@inheritdoc}
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
        $this->reflectionClass = new \ReflectionClass($this->name);
    }
}
