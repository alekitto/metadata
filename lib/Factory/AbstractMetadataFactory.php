<?php

declare(strict_types=1);

namespace Kcs\Metadata\Factory;

use Kcs\Metadata\ClassMetadataInterface;
use Kcs\Metadata\Event\ClassMetadataLoadedEvent;
use Kcs\Metadata\Exception\InvalidArgumentException;
use Kcs\Metadata\Exception\InvalidMetadataException;
use Kcs\Metadata\Loader\LoaderInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use ReflectionClass;

use function assert;
use function class_exists;
use function get_class;
use function interface_exists;
use function is_bool;
use function is_object;
use function is_string;
use function ltrim;
use function Safe\preg_replace;
use function str_replace;

abstract class AbstractMetadataFactory implements MetadataFactoryInterface
{
    private LoaderInterface $loader;
    private ?EventDispatcherInterface $eventDispatcher;
    private ?CacheItemPoolInterface $cache;

    /** @var array<string, ClassMetadataInterface> */
    private array $loadedClasses;

    public function __construct(LoaderInterface $loader, ?EventDispatcherInterface $eventDispatcher = null, ?CacheItemPoolInterface $cache = null)
    {
        $this->loader = $loader;
        $this->eventDispatcher = $eventDispatcher;
        $this->cache = $cache;

        $this->loadedClasses = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadataFor($value): ClassMetadataInterface
    {
        $class = $this->getClass($value);
        if (empty($class)) {
            throw InvalidArgumentException::create(InvalidArgumentException::VALUE_IS_NOT_AN_OBJECT, $value);
        }

        assert(! is_bool($class));

        if (isset($this->loadedClasses[$class])) {
            return $this->loadedClasses[$class];
        }

        if ($this->cache !== null) {
            $cacheKey = preg_replace('#[\{\}\(\)/\\\\@:]#', '_', str_replace('_', '__', $class));
            $item = $this->cache->getItem($cacheKey);
            if ($item->isHit()) {
                return $this->loadedClasses[$class] = $item->get();
            }
        }

        if (! class_exists($class) && ! interface_exists($class)) {
            throw InvalidArgumentException::create(InvalidArgumentException::CLASS_DOES_NOT_EXIST, $class);
        }

        $reflectionClass = new ReflectionClass($class);
        $classMetadata = $this->createMetadata($reflectionClass);
        if (! $this->loader->loadClassMetadata($classMetadata)) {
            return $classMetadata;
        }

        $this->mergeSuperclasses($classMetadata);
        $classMetadata->finalize();

        $this->validate($classMetadata);
        $this->dispatchClassMetadataLoadedEvent($classMetadata);

        if (isset($item)) {
            assert($this->cache !== null);

            $item->set($classMetadata);
            $this->cache->save($item);
        }

        return $this->loadedClasses[$class] = $classMetadata;
    }

    /**
     * {@inheritdoc}
     */
    public function hasMetadataFor($value): bool
    {
        $class = $this->getClass($value);
        if (is_bool($class)) {
            return false;
        }

        return class_exists($class) || interface_exists($class);
    }

    protected function mergeSuperclasses(ClassMetadataInterface $classMetadata): void
    {
        $reflectionClass = $classMetadata->getReflectionClass();

        $parent = $reflectionClass->getParentClass();
        if ($parent) {
            $classMetadata->merge($this->getMetadataFor($parent->name));
        }

        foreach ($reflectionClass->getInterfaces() as $interface) {
            $classMetadata->merge($this->getMetadataFor($interface->name));
        }
    }

    /**
     * Create a new instance of metadata object for this factory.
     */
    abstract protected function createMetadata(ReflectionClass $class): ClassMetadataInterface;

    /**
     * Validate loaded metadata
     * MUST throw {@see InvalidMetadataException} if validation error occurs.
     *
     * @throws InvalidMetadataException
     */
    protected function validate(ClassMetadataInterface $classMetadata): void
    {
    }

    /**
     * Dispatches a class metadata loaded event for the given class.
     */
    protected function dispatchClassMetadataLoadedEvent(ClassMetadataInterface $classMetadata): void
    {
        if ($this->eventDispatcher === null) {
            return;
        }

        $this->eventDispatcher->dispatch(new ClassMetadataLoadedEvent($classMetadata));
    }

    /**
     * Get the class name from a string or an object.
     *
     * @param string|object $value
     *
     * @return string|bool
     * @phpstan-return class-string|bool
     */
    private function getClass($value)
    {
        /* @phpstan-ignore-next-line */
        if (! is_object($value) && ! is_string($value)) {
            return false;
        }

        if (is_object($value)) {
            $value = get_class($value);
        }

        /* @phpstan-ignore-next-line */
        return ltrim($value, '\\');
    }
}
