<?php

namespace Kcs\Metadata\Factory;

use Doctrine\Common\Cache\Cache;
use Kcs\Metadata\ClassMetadataInterface;
use Kcs\Metadata\Event\ClassMetadataLoadedEvent;
use Kcs\Metadata\Exception\InvalidArgumentException;
use Kcs\Metadata\Exception\InvalidMetadataException;
use Kcs\Metadata\Loader\LoaderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractMetadataFactory implements MetadataFactoryInterface
{
    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var array
     */
    private $loadedClasses;

    public function __construct(LoaderInterface $loader, EventDispatcherInterface $eventDispatcher = null, Cache $cache = null)
    {
        $this->loader = $loader;
        $this->eventDispatcher = $eventDispatcher;
        $this->cache = $cache;

        $this->loadedClasses = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadataFor($value)
    {
        $class = $this->getClass($value);
        if (empty($class)) {
            throw InvalidArgumentException::create(InvalidArgumentException::VALUE_IS_NOT_AN_OBJECT, $value);
        }

        if (isset($this->loadedClasses[$class])) {
            return $this->loadedClasses[$class];
        }

        if (null !== $this->cache && ($this->loadedClasses[$class] = $this->cache->fetch($class) ?: null)) {
            return $this->loadedClasses[$class];
        }

        if (! class_exists($class) && ! interface_exists($class)) {
            throw InvalidArgumentException::create(InvalidArgumentException::CLASS_DOES_NOT_EXIST, $class);
        }

        $reflectionClass = new \ReflectionClass($class);
        $classMetadata = $this->createMetadata($reflectionClass);
        if (! $this->loader->loadClassMetadata($classMetadata)) {
            return $classMetadata;
        }

        $this->mergeSuperclasses($classMetadata);
        $this->validate($classMetadata);

        if ($this->eventDispatcher) {
            $this->eventDispatcher->dispatch(
                ClassMetadataLoadedEvent::LOADED_EVENT,
                new ClassMetadataLoadedEvent($classMetadata)
            );
        }

        if (null !== $this->cache) {
            $this->cache->save($class, $classMetadata);
        }

        return $this->loadedClasses[$class] = $classMetadata;
    }

    /**
     * {@inheritdoc}
     */
    public function hasMetadataFor($value)
    {
        $class = $this->getClass($value);

        return ! empty($class) && (class_exists($class) || interface_exists($class));
    }

    public function setCache(Cache $cache = null)
    {
        $this->cache = $cache;
    }

    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher = null)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    protected function mergeSuperclasses(ClassMetadataInterface $classMetadata)
    {
        $reflectionClass = $classMetadata->getReflectionClass();

        if ($parent = $reflectionClass->getParentClass()) {
            $classMetadata->merge($this->getMetadataFor($parent->name));
        }

        foreach ($reflectionClass->getInterfaces() as $interface) {
            $classMetadata->merge($this->getMetadataFor($interface->name));
        }
    }

    /**
     * Create a new instance of metadata object for this factory
     *
     * @param \ReflectionClass $class
     *
     * @return ClassMetadataInterface
     */
    abstract protected function createMetadata(\ReflectionClass $class);

    /**
     * Validate loaded metadata
     * MUST throw {@see InvalidMetadataException} if validation error occurs.
     *
     * @param ClassMetadataInterface $classMetadata
     *
     * @throws InvalidMetadataException
     */
    protected function validate(ClassMetadataInterface $classMetadata)
    {
    }

    /**
     * Get the class name from a string or an object
     *
     * @param string|object $value
     *
     * @return string|bool
     */
    private function getClass($value)
    {
        if (! is_object($value) && ! is_string($value)) {
            return false;
        }

        if (is_object($value)) {
            $value = get_class($value);
        }

        return ltrim($value, '\\');
    }
}
