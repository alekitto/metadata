<?php

declare(strict_types=1);

namespace Kcs\Metadata\Loader;

use Kcs\Metadata\ClassMetadataInterface;
use Kcs\Metadata\Loader\Processor\ProcessorFactoryInterface;
use Kcs\Metadata\MetadataInterface;
use Kcs\Metadata\MethodMetadata;
use Kcs\Metadata\PropertyMetadata;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

abstract class AbstractProcessorLoader implements LoaderInterface
{
    public function __construct(protected ProcessorFactoryInterface $processorFactory)
    {
    }

    public function loadClassMetadata(ClassMetadataInterface $classMetadata): bool
    {
        $reflectionClass = $classMetadata->getReflectionClass();
        $this->processClassDescriptors($classMetadata, $this->getClassDescriptors($reflectionClass));

        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            $attributeMetadata = $this->createMethodMetadata($reflectionMethod);
            $this->processMethodDescriptors($attributeMetadata, $this->getMethodDescriptors($reflectionMethod));

            $classMetadata->addAttributeMetadata($attributeMetadata);
        }

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $attributeMetadata = $this->createPropertyMetadata($reflectionProperty);
            $this->processPropertyDescriptors($attributeMetadata, $this->getPropertyDescriptors($reflectionProperty));

            $classMetadata->addAttributeMetadata($attributeMetadata);
        }

        return true;
    }

    /**
     * Get class metadata descriptors (ex: annotation objects).
     *
     * @return object[]
     */
    abstract protected function getClassDescriptors(ReflectionClass $reflectionClass): array;

    /**
     * Get method metadata descriptors (ex: annotation objects).
     *
     * @return object[]
     */
    abstract protected function getMethodDescriptors(ReflectionMethod $reflectionMethod): array;

    /**
     * Get property metadata descriptors (ex: annotation objects).
     *
     * @return object[]
     */
    abstract protected function getPropertyDescriptors(ReflectionProperty $reflectionProperty): array;

    /**
     * Create method metadata object.
     */
    protected function createMethodMetadata(ReflectionMethod $reflectionMethod): MetadataInterface
    {
        // @phpstan-ignore-next-line
        return new MethodMetadata($reflectionMethod->class, $reflectionMethod->name);
    }

    /**
     * Create property metadata object.
     */
    protected function createPropertyMetadata(ReflectionProperty $reflectionProperty): MetadataInterface
    {
        // @phpstan-ignore-next-line
        return new PropertyMetadata($reflectionProperty->class, $reflectionProperty->name);
    }

    /**
     * Process class descriptors.
     *
     * @param object[] $descriptors
     */
    protected function processClassDescriptors(ClassMetadataInterface $classMetadata, array $descriptors): void
    {
        $this->doLoadFromDescriptors($classMetadata, $descriptors);
    }

    /**
     * Process method descriptors.
     *
     * @param object[] $descriptors
     */
    protected function processMethodDescriptors(MetadataInterface $metadata, array $descriptors): void
    {
        $this->doLoadFromDescriptors($metadata, $descriptors);
    }

    /**
     * Process property descriptors.
     *
     * @param object[] $descriptors
     */
    protected function processPropertyDescriptors(MetadataInterface $metadata, array $descriptors): void
    {
        $this->doLoadFromDescriptors($metadata, $descriptors);
    }

    /**
     * Call processors.
     *
     * @param object[] $descriptors
     */
    private function doLoadFromDescriptors(MetadataInterface $metadata, array $descriptors): void
    {
        foreach ($descriptors as $descriptor) {
            $processor = $this->processorFactory->getProcessor($descriptor);
            if ($processor === null) {
                continue;
            }

            $processor->process($metadata, $descriptor);
        }
    }
}
