<?php declare(strict_types=1);

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
    public function __construct(string $class, string $name)
    {
        $this->class = $class;
        $this->name = $name;
    }

    public function getReflection(): \ReflectionMethod
    {
        if (null === $this->reflectionMethod) {
            $this->reflectionMethod = new \ReflectionMethod($this->class, $this->name);
        }

        return $this->reflectionMethod;
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
