<?php

declare(strict_types=1);

namespace Kcs\Metadata\Loader;

use Kcs\Metadata\Loader\Processor\ProcessorFactoryInterface;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use RuntimeException;

use function array_map;

use const PHP_VERSION_ID;

class AttributesProcessorLoader extends AbstractProcessorLoader
{
    public function __construct(ProcessorFactoryInterface $processorFactory)
    {
        parent::__construct($processorFactory);

        if (PHP_VERSION_ID < 80000) {
            throw new RuntimeException(self::class . ' can be used only on PHP >= 8.0');
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function getClassDescriptors(ReflectionClass $reflectionClass): array
    {
        return array_map(static fn (ReflectionAttribute $attribute) => $attribute->newInstance(), $reflectionClass->getAttributes());
    }

    /**
     * {@inheritDoc}
     */
    protected function getMethodDescriptors(ReflectionMethod $reflectionMethod): array
    {
        return array_map(static fn (ReflectionAttribute $attribute) => $attribute->newInstance(), $reflectionMethod->getAttributes());
    }

    /**
     * {@inheritDoc}
     */
    protected function getPropertyDescriptors(ReflectionProperty $reflectionProperty): array
    {
        return array_map(static fn (ReflectionAttribute $attribute) => $attribute->newInstance(), $reflectionProperty->getAttributes());
    }
}
