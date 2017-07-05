<?php

namespace Kcs\Metadata\Loader;

use Doctrine\Common\Annotations\Reader;

class AnnotationProcessorLoader extends AbstractProcessorLoader
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * Set annotation reader
     *
     * @param Reader $reader
     */
    public function setReader(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    protected function getClassDescriptors(\ReflectionClass $reflectionClass)
    {
        return $this->reader->getClassAnnotations($reflectionClass);
    }

    /**
     * {@inheritdoc}
     */
    protected function getMethodDescriptors(\ReflectionMethod $reflectionMethod)
    {
        return $this->reader->getMethodAnnotations($reflectionMethod);
    }

    /**
     * {@inheritdoc}
     */
    protected function getPropertyDescriptors(\ReflectionProperty $reflectionProperty)
    {
        return $this->reader->getPropertyAnnotations($reflectionProperty);
    }
}
