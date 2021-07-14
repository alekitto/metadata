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
use function array_shift;
use function assert;
use function class_exists;
use function count;
use function get_class;
use function is_array;
use function is_object;

use const PHP_VERSION_ID;

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

        $this->registerProcessorsByAnnotations($dir);
        $this->registerProcessorsByAttributes($dir);
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

    public function registerProcessorsByAnnotations(string $dir): void
    {
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

    public function registerProcessorsByAttributes(string $dir): void
    {
        if (PHP_VERSION_ID < 80000) {
            return;
        }

        $finder = new RecursiveFinder($dir);
        $finder
            ->withAttribute(Processor::class)
            ->implementationOf(ProcessorInterface::class);

        foreach ($finder as $reflClass) {
            assert($reflClass instanceof ReflectionClass);
            $attributes = $reflClass->getAttributes(Processor::class);

            if (count($attributes) === 0) {
                continue;
            }

            $annot = array_shift($attributes)->newInstance();
            assert($annot instanceof Processor);

            $this->registerProcessor($annot->annotation, $reflClass->getName());
        }
    }
}
