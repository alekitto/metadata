<?php

declare(strict_types=1);

namespace Kcs\Metadata\Loader\Processor;

use Doctrine\Common\Annotations\AnnotationReader;
use Kcs\ClassFinder\Finder\RecursiveFinder;
use Kcs\Metadata\Exception\InvalidArgumentException;
use Kcs\Metadata\Loader\Processor\Annotation\Processor;
use ReflectionClass;
use RuntimeException;

use function array_map;
use function assert;
use function class_exists;
use function get_class;
use function is_array;
use function is_object;

class ProcessorFactory implements ProcessorFactoryInterface
{
    /**
     * @var array<string, string|string[]>
     * @phpstan-var array<class-string, class-string|class-string[]>
     */
    private array $processors = [];

    /** @var ProcessorInterface[] */
    private array $instances = [];

    /**
     * Register a processor class for $class.
     *
     * @phpstan-param class-string $class
     * @phpstan-param class-string $processorClass
     */
    public function registerProcessor(string $class, string $processorClass): void
    {
        if (! (new ReflectionClass($processorClass))->implementsInterface(ProcessorInterface::class)) {
            throw InvalidArgumentException::create(InvalidArgumentException::INVALID_PROCESSOR_INTERFACE_CLASS, $processorClass);
        }

        if (! isset($this->processors[$class])) {
            $this->processors[$class] = $processorClass;
        } elseif (! is_array($this->processors[$class])) {
            $this->processors[$class] = [$this->processors[$class], $processorClass];
        } else {
            $this->processors[$class][] = $processorClass;
        }
    }

    /**
     * Finds and register annotation processors recursively.
     */
    public function registerProcessors(string $dir): void
    {
        if (! class_exists(RecursiveFinder::class)) {
            throw new RuntimeException('Cannot find processors as the kcs/class-finder package is not installed.');
        }

        $reader = new AnnotationReader();
        $finder = new RecursiveFinder($dir);
        $finder
            ->annotatedBy(Processor::class)
            ->implementationOf(ProcessorInterface::class);

        foreach ($finder as $reflClass) {
            assert($reflClass instanceof ReflectionClass);
            $annot = $reader->getClassAnnotation($reflClass, Processor::class);

            assert($annot instanceof Processor);
            $this->registerProcessor($annot->annotation, $reflClass->getName());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getProcessor($class): ?ProcessorInterface
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        if (! isset($this->processors[$class])) {
            return null;
        }

        if (isset($this->instances[$class])) {
            return $this->instances[$class];
        }

        $processor = $this->processors[$class];
        if (is_array($processor)) {
            return $this->instances[$class] = $this->createComposite($processor);
        }

        return $this->instances[$class] = new $processor();
    }

    /**
     * Create a CompositeProcessor instance.
     *
     * @param string[] $processors
     *
     * @phpstan-param class-string[] $processors
     */
    private function createComposite(array $processors): CompositeProcessor
    {
        return new CompositeProcessor(array_map([self::class, 'instantiateProcessor'], $processors));
    }

    /**
     * @phpstan-param class-string $processorClass
     */
    private static function instantiateProcessor(string $processorClass): ProcessorInterface // phpcs:ignore SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedMethod
    {
        return new $processorClass();
    }
}
