<?php

namespace Kcs\Metadata\Loader;

use Kcs\Metadata\ClassMetadataInterface;
use Kcs\Metadata\Loader\Processor\ProcessorFactoryInterface;
use Kcs\Metadata\MetadataInterface;
use Kcs\Metadata\MethodMetadata;
use Kcs\Metadata\PropertyMetadata;

abstract class AbstractProcessorLoader implements LoaderInterface
{
    /**
     * @var ProcessorFactoryInterface
     */
    protected $processorFactory;

    public function __construct(ProcessorFactoryInterface $processorFactory)
    {
        $this->processorFactory = $processorFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function loadClassMetadata(ClassMetadataInterface $classMetadata)
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
     * Get class metadata descriptors (ex: annotation objects)
     *
     * @param \ReflectionClass $reflectionClass
     *
     * @return array
     */
    abstract protected function getClassDescriptors(\ReflectionClass $reflectionClass);

    /**
     * Get method metadata descriptors (ex: annotation objects)
     *
     * @param \ReflectionMethod $reflectionMethod
     *
     * @return array
     */
    abstract protected function getMethodDescriptors(\ReflectionMethod $reflectionMethod);

    /**
     * Get property metadata descriptors (ex: annotation objects)
     *
     * @param \ReflectionProperty $reflectionProperty
     *
     * @return array
     */
    abstract protected function getPropertyDescriptors(\ReflectionProperty $reflectionProperty);

    /**
     * Create method metadata object
     *
     * @param \ReflectionMethod $reflectionMethod
     *
     * @return MetadataInterface
     */
    protected function createMethodMetadata(\ReflectionMethod $reflectionMethod)
    {
        return new MethodMetadata($reflectionMethod->class, $reflectionMethod->name);
    }

    /**
     * Create property metadata object
     *
     * @param \ReflectionProperty $reflectionProperty
     *
     * @return MetadataInterface
     */
    protected function createPropertyMetadata(\ReflectionProperty $reflectionProperty)
    {
        return new PropertyMetadata($reflectionProperty->class, $reflectionProperty->name);
    }

    /**
     * Process class descriptors
     *
     * @param ClassMetadataInterface $classMetadata
     * @param array $descriptors
     */
    protected function processClassDescriptors(ClassMetadataInterface $classMetadata, array $descriptors)
    {
        $this->doLoadFromDescriptors($classMetadata, $descriptors);
    }

    /**
     * Process method descriptors
     *
     * @param MetadataInterface $metadata
     * @param array $descriptors
     */
    protected function processMethodDescriptors(MetadataInterface $metadata, array $descriptors)
    {
        $this->doLoadFromDescriptors($metadata, $descriptors);
    }

    /**
     * Process property descriptors
     *
     * @param MetadataInterface $metadata
     * @param array $descriptors
     */
    protected function processPropertyDescriptors(MetadataInterface $metadata, array $descriptors)
    {
        $this->doLoadFromDescriptors($metadata, $descriptors);
    }

    /**
     * Call processors
     *
     * @param MetadataInterface $metadata
     * @param array $descriptors
     */
    private function doLoadFromDescriptors(MetadataInterface $metadata, array $descriptors)
    {
        foreach ($descriptors as $descriptor) {
            $processor = $this->processorFactory->getProcessor($descriptor);
            if (null === $processor) {
                continue;
            }

            $processor->process($metadata, $descriptor);
        }
    }
}
