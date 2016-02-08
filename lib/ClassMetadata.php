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
     * @var MetadataInterface[]
     */
    public $attributesMetadata;

    public function __construct(\ReflectionClass $class)
    {
        $this->reflectionClass = $class;
        $this->attributesMetadata = array ();
    }

    /**
     * @inheritDoc
     */
    public function getReflectionClass()
    {
        return $this->reflectionClass;
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function getAttributeMetadata($name)
    {
        if (! isset($this->attributesMetadata[$name])) {
            return new NullMetadata($name);
        }

        return $this->attributesMetadata[$name];
    }

    /**
     * @inheritDoc
     */
    public function getAttributesMetadata()
    {
        return $this->attributesMetadata;
    }

    /**
     * @inheritDoc
     */
    public function addAttributeMetadata(MetadataInterface $metadata)
    {
        $this->attributesMetadata[ $metadata->getName() ] = $metadata;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getReflectionClass()->name;
    }

    /**
     * @inheritDoc
     */
    public function __sleep()
    {
        return $this->getPublicPropertiesName();
    }
}
