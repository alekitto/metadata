<?php declare(strict_types=1);

namespace Kcs\Metadata\Factory;

use Doctrine\Common\Cache\Cache;
use Kcs\Metadata\ClassMetadataInterface;
use Kcs\Metadata\Event\ClassMetadataLoadedEvent;
use Kcs\Metadata\Exception\InvalidArgumentException;
use Kcs\Metadata\Exception\InvalidMetadataException;
use Kcs\Metadata\Loader\LoaderInterface;
use Psr\Cache\CacheItemPoolInterface;
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
     * @var Cache|CacheItemPoolInterface
     */
    private $cache;

    /**
     * @var array
     */
    private $loadedClasses;

    public function __construct(LoaderInterface $loader, EventDispatcherInterface $eventDispatcher = null, $cache = null)
    {
        $this->loader = $loader;
        $this->eventDispatcher = $eventDispatcher;
        $this->cache = $cache;

        if (null !== $cache && ! ($cache instanceof Cache || $cache instanceof CacheItemPoolInterface)) {
            throw new \TypeError(
                'Argument 3 passed to '.get_class($this).' must be '.Cache::class.
                ' or a '.CacheItemPoolInterface::class.'. '.(is_object($cache) ? get_class($cache) : gettype($cache)).' passed.');
        }

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

        if (isset($this->loadedClasses[$class])) {
            return $this->loadedClasses[$class];
        }

        if (null !== $this->loadedClasses[$class] = $this->getFromCache($class)) {
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

        $this->dispatchClassMetadataLoadedEvent($classMetadata);
        $this->saveInCache($class, $classMetadata);

        return $this->loadedClasses[$class] = $classMetadata;
    }

    /**
     * {@inheritdoc}
     */
    public function hasMetadataFor($value): bool
    {
        $class = $this->getClass($value);

        return ! empty($class) && (class_exists($class) || interface_exists($class));
    }

    protected function mergeSuperclasses(ClassMetadataInterface $classMetadata): void
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
     * Create a new instance of metadata object for this factory.
     *
     * @param \ReflectionClass $class
     *
     * @return ClassMetadataInterface
     */
    abstract protected function createMetadata(\ReflectionClass $class): ClassMetadataInterface;

    /**
     * Validate loaded metadata
     * MUST throw {@see InvalidMetadataException} if validation error occurs.
     *
     * @param ClassMetadataInterface $classMetadata
     *
     * @throws InvalidMetadataException
     */
    protected function validate(ClassMetadataInterface $classMetadata): void
    {
    }

    /**
     * Dispatches a class metadata loaded event for the given class.
     *
     * @param ClassMetadataInterface $classMetadata
     */
    protected function dispatchClassMetadataLoadedEvent(ClassMetadataInterface $classMetadata): void
    {
        if (null === $this->eventDispatcher) {
            return;
        }

        $this->eventDispatcher->dispatch(
            ClassMetadataLoadedEvent::LOADED_EVENT,
            new ClassMetadataLoadedEvent($classMetadata)
        );
    }

    /**
     * Check a cache pool for cached metadata.
     *
     * @param string $class
     *
     * @return null|ClassMetadataInterface
     */
    private function getFromCache(string $class): ?ClassMetadataInterface
    {
        if (null === $this->cache) {
            return null;
        }

        if ($this->cache instanceof Cache) {
            $cached = $this->cache->fetch($class) ?: null;
        } else {
            $class = str_replace(['_', '\\'], ['__', '_'], $class);
            $item = $this->cache->getItem($class);
            $cached = $item->isHit() ? $item->get() : null;
        }

        return $cached;
    }

    /**
     * Saves a metadata into a cache pool.
     *
     * @param string                 $class
     * @param ClassMetadataInterface $classMetadata
     */
    private function saveInCache(string $class, ClassMetadataInterface $classMetadata): void
    {
        if (null === $this->cache) {
            return;
        }

        if ($this->cache instanceof Cache) {
            $this->cache->save($class, $classMetadata);
        } else {
            $class = str_replace(['_', '\\'], ['__', '_'], $class);
            $item = $this->cache->getItem($class);
            $item->set($classMetadata);

            $this->cache->save($class);
        }
    }

    /**
     * Get the class name from a string or an object.
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
