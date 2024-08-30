<?php

declare(strict_types=1);

namespace Kcs\Metadata\Loader;

use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

class AnnotationProcessorLoader extends AbstractProcessorLoader
{
    private Reader $reader;

    /**
     * Set annotation reader.
     */
    public function setReader(Reader $reader): void
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritDoc}
     */
    protected function getClassDescriptors(ReflectionClass $reflectionClass): array
    {
        return $this->reader->getClassAnnotations($reflectionClass);
    }

    /**
     * {@inheritDoc}
     */
    protected function getMethodDescriptors(ReflectionMethod $reflectionMethod): array
    {
        return $this->reader->getMethodAnnotations($reflectionMethod);
    }

    /**
     * {@inheritDoc}
     */
    protected function getPropertyDescriptors(ReflectionProperty $reflectionProperty): array
    {
        return $this->reader->getPropertyAnnotations($reflectionProperty);
    }
}
