<?php declare(strict_types=1);

namespace Kcs\Metadata\Loader;

use Doctrine\Common\Annotations\Reader;

class AnnotationProcessorLoader extends AbstractProcessorLoader
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * Set annotation reader.
     *
     * @param Reader $reader
     */
    public function setReader(Reader $reader): void
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    protected function getClassDescriptors(\ReflectionClass $reflectionClass): array
    {
        return $this->reader->getClassAnnotations($reflectionClass);
    }

    /**
     * {@inheritdoc}
     */
    protected function getMethodDescriptors(\ReflectionMethod $reflectionMethod): array
    {
        return $this->reader->getMethodAnnotations($reflectionMethod);
    }

    /**
     * {@inheritdoc}
     */
    protected function getPropertyDescriptors(\ReflectionProperty $reflectionProperty): array
    {
        return $this->reader->getPropertyAnnotations($reflectionProperty);
    }
}
