<?php declare(strict_types=1);

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
    public function __construct(string $class, string $name)
    {
        $this->class = $class;
        $this->name = $name;
    }

    public function getReflection(): \ReflectionProperty
    {
        if (null === $this->reflectionProperty) {
            $this->reflectionProperty = new \ReflectionProperty($this->class, $this->name);
        }

        return $this->reflectionProperty;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(MetadataInterface $metadata): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
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
