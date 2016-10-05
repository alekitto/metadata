<?php

namespace Kcs\Metadata\Loader;

use Kcs\Metadata\ClassMetadataInterface;
use Kcs\Metadata\Loader\Processor\ProcessorFactoryInterface;
use Kcs\Metadata\MetadataInterface;

abstract class AbstractProcessorLoader implements LoaderInterface
{
    /**
     * @var ProcessorFactoryInterface
     */
    private $processorFactory;

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
        $this->doLoadFromDescriptors($classMetadata, $this->getClassDescriptors($reflectionClass));

        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            $attributeMetadata = $this->createMethodMetadata($reflectionMethod);
            $this->doLoadFromDescriptors($attributeMetadata, $this->getMethodDescriptors($reflectionMethod));
        }

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $attributeMetadata = $this->createPropertyMetadata($reflectionProperty);
            $this->doLoadFromDescriptors($attributeMetadata, $this->getPropertyDescriptors($reflectionProperty));
        }
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
    abstract protected function createMethodMetadata(\ReflectionMethod $reflectionMethod);

    /**
     * Create property metadata object
     *
     * @param \ReflectionProperty $reflectionProperty
     *
     * @return MetadataInterface
     */
    abstract protected function createPropertyMetadata(\ReflectionProperty $reflectionProperty);

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
