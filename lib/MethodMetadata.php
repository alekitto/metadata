<?php declare(strict_types=1);

namespace Kcs\Metadata;

class MethodMetadata implements MetadataInterface
{
    use AttributeMetadataTrait;
    use MetadataPropertiesTrait;

    /**
     * @var \ReflectionMethod
     */
    private $reflectionMethod;

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
}
