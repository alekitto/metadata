<?php declare(strict_types=1);

namespace Kcs\Metadata\Factory;

use Kcs\Metadata\ClassMetadataInterface;
use Kcs\Metadata\Event\ClassMetadataLoadedEvent;
use Kcs\Metadata\Exception\InvalidArgumentException;
use Kcs\Metadata\Exception\InvalidMetadataException;
use Kcs\Metadata\Loader\LoaderInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

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
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * @var array
     */
    private $loadedClasses;

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

        if (isset($this->loadedClasses[$class])) {
            return $this->loadedClasses[$class];
        }

        if (null !== $this->cache) {
            $cacheKey = \preg_replace('#[\{\}\(\)/\\\\@:]#', '_', \str_replace('_', '__', $class));
            $item = $this->cache->getItem($cacheKey);
            if ($item->isHit()) {
                return $this->loadedClasses[$class] = $item->get();
            }
        }

        if (! \class_exists($class) && ! \interface_exists($class)) {
            throw InvalidArgumentException::create(InvalidArgumentException::CLASS_DOES_NOT_EXIST, $class);
        }

        $reflectionClass = new \ReflectionClass($class);
        $classMetadata = $this->createMetadata($reflectionClass);
        if (! $this->loader->loadClassMetadata($classMetadata)) {
            return $classMetadata;
        }

        $this->mergeSuperclasses($classMetadata);
        if (\method_exists($classMetadata, 'finalize')) {
            $classMetadata->finalize();
        }

        $this->validate($classMetadata);

        $this->dispatchClassMetadataLoadedEvent($classMetadata);

        if (isset($item)) {
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

        return ! empty($class) && (\class_exists($class) || \interface_exists($class));
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

        $this->eventDispatcher->dispatch(new ClassMetadataLoadedEvent($classMetadata));
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
        if (! \is_object($value) && ! \is_string($value)) {
            return false;
        }

        if (\is_object($value)) {
            $value = \get_class($value);
        }

        return \ltrim($value, '\\');
    }
}
