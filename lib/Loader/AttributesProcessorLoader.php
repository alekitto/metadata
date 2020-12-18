<?php declare(strict_types=1);

namespace Kcs\Metadata\Loader;

use Kcs\Metadata\Loader\Processor\ProcessorFactoryInterface;

class AttributesProcessorLoader extends AbstractProcessorLoader
{
    public function __construct(ProcessorFactoryInterface $processorFactory)
    {
        parent::__construct($processorFactory);

        if (PHP_VERSION_ID < 80000) {
            throw new \RuntimeException(__CLASS__ . ' can be used only on PHP >= 8.0');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getClassDescriptors(\ReflectionClass $reflectionClass): array
    {
        return array_map(static fn (\ReflectionAttribute $attribute) => $attribute->newInstance(), $reflectionClass->getAttributes());
    }

    /**
     * {@inheritdoc}
     */
    protected function getMethodDescriptors(\ReflectionMethod $reflectionMethod): array
    {
        return array_map(static fn (\ReflectionAttribute $attribute) => $attribute->newInstance(), $reflectionMethod->getAttributes());
    }

    /**
     * {@inheritdoc}
     */
    protected function getPropertyDescriptors(\ReflectionProperty $reflectionProperty): array
    {
        return array_map(static fn (\ReflectionAttribute $attribute) => $attribute->newInstance(), $reflectionProperty->getAttributes());
    }
}
