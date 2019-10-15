<?php declare(strict_types=1);

namespace Kcs\Metadata;

use Kcs\Metadata\Exception\InvalidArgumentException;

class ClassMetadata implements ClassMetadataInterface
{
    use MetadataPropertiesTrait {
        __wakeup as public traitWakeup;
    }

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

    /**
     * @var string[]
     */
    private $attributesNames;

    public function __construct(\ReflectionClass $class)
    {
        $this->reflectionClass = $class;
        $this->name = $class->name;
        $this->attributesMetadata = [];
        $this->attributesNames = [];
    }

    public function __wakeup()
    {
        $this->traitWakeup();

        foreach ($this->attributesMetadata as $key => $metadata) {
            $this->attributesNames[\strtolower($key)] = $key;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getReflectionClass(): \ReflectionClass
    {
        if (null === $this->reflectionClass) {
            $this->reflectionClass = new \ReflectionClass($this->name);
        }

        return $this->reflectionClass;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(MetadataInterface $metadata): void
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
                $this->attributesNames[\strtolower($attrName)] = $attrName;
                continue;
            }

            $target->merge($attrMetadata);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeMetadata(string $name): MetadataInterface
    {
        $name = \strtolower($name);
        if (! isset($this->attributesNames[$name])) {
            return new NullMetadata($name);
        }

        $name = $this->attributesNames[$name];

        return $this->attributesMetadata[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributesMetadata(): array
    {
        return $this->attributesMetadata;
    }

    /**
     * {@inheritdoc}
     */
    public function addAttributeMetadata(MetadataInterface $metadata): void
    {
        $name = $metadata->getName();
        $this->attributesMetadata[$name] = $metadata;
        $this->attributesNames[\strtolower($name)] = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->getReflectionClass()->name;
    }

    /**
     * {@inheritdoc}
     */
    public function finalize(): void
    {
    }
}
