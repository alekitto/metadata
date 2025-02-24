<?php declare(strict_types=1);

namespace Kcs\Metadata\Tests\Factory;

use Kcs\Metadata\ClassMetadata;
use Kcs\Metadata\ClassMetadataInterface;
use Kcs\Metadata\Event\ClassMetadataLoadedEvent;
use Kcs\Metadata\Exception\InvalidArgumentException;
use Kcs\Metadata\Factory\MetadataFactory;
use Kcs\Metadata\Loader\LoaderInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Cache\CacheItemPoolInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class MockedClassMetadataFactory extends MetadataFactory
{
    /**
     * @var null|ObjectProphecy|ClassMetadata
     */
    public $mock = null;

    /**
     * {@inheritdoc}
     */
    protected function createMetadata(\ReflectionClass $class): ClassMetadataInterface
    {
        if (null !== $this->mock) {
            $mock = $this->mock;
            $this->mock = null;

            return $mock;
        }

        return parent::createMetadata($class);
    }
}

class FakeClassMetadata extends ClassMetadata
{
    public bool $finalized = false;

    public function finalize(): void
    {
        $this->finalized = true;
    }
}

class FakeClassNoMetadata
{
}

class MetadataFactoryTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy|LoaderInterface $loader;
    private ObjectProphecy|CacheItemPoolInterface $cache;

    protected function setUp(): void
    {
        $this->loader = $this->prophesize(LoaderInterface::class);
        $this->cache = $this->prophesize(CacheItemPoolInterface::class);
    }

    /**
     * @test
     */
    #[Test]
    public function has_metadata_should_return_false_on_non_existent_classes(): void
    {
        $factory = new MetadataFactory($this->loader->reveal());
        self::assertFalse($factory->hasMetadataFor('NonExistentClass'));
    }

    /**
     * @test
     */
    #[Test]
    public function has_metadata_should_return_false_on_invalid_value(): void
    {
        $factory = new MetadataFactory($this->loader->reveal());
        self::assertFalse($factory->hasMetadataFor([]));
    }

    public static function invalid_value_provider(): array
    {
        return [
            [[]],
            ['NonExistentClass'],
        ];
    }

    /**
     * @test
     * @dataProvider invalid_value_provider
     */
    #[Test]
    #[DataProvider('invalid_value_provider')]
    public function get_metadata_should_throw_if_invalid_value_has_passed($value): void
    {
        $this->expectException(InvalidArgumentException::class);
        $factory = new MetadataFactory($this->loader->reveal());
        $factory->getMetadataFor($value);
    }

    /**
     * @test
     */
    #[Test]
    public function get_metadata_for_should_call_the_metadata_loader(): void
    {
        $this->loader->loadClassMetadata(Argument::type(ClassMetadataInterface::class))
            ->shouldBeCalled();

        $factory = new MetadataFactory($this->loader->reveal());
        $factory->getMetadataFor($this);
    }

    /**
     * @test
     */
    #[Test]
    public function get_metadata_for_should_not_call_the_metadata_loader_twice(): void
    {
        $className = \get_class($this);
        $this->loader->loadClassMetadata(Argument::that(function (ClassMetadataInterface $metadata) use ($className) {
            return $metadata->getReflectionClass()->name === $className;
        }))
            ->willReturn(true)
            ->shouldBeCalledTimes(1);

        $this->loader->loadClassMetadata(Argument::type(ClassMetadataInterface::class))->willReturn(true);

        $factory = new MetadataFactory($this->loader->reveal());
        $factory->getMetadataFor($this);
        $factory->getMetadataFor($this);
    }

    /**
     * @test
     */
    #[Test]
    public function get_metadata_for_should_load_data_from_cache(): void
    {
        $className = \get_class($this);
        $metadata = new ClassMetadata(new \ReflectionClass($this));

        $this->cache->getItem(\str_replace('\\', '_', $className))
            ->willReturn($item = $this->prophesize(ItemInterface::class));
        $item->isHit()->willReturn(true);
        $item->get()->willReturn($metadata);

        $this->loader->loadClassMetadata(Argument::type(ClassMetadataInterface::class))->shouldNotBeCalled();

        $factory = new MetadataFactory($this->loader->reveal(), null, $this->cache->reveal());
        $factory->getMetadataFor($this);
    }

    /**
     * @test
     */
    #[Test]
    public function set_metadata_for_should_store_data_to_cache(): void
    {
        $className = \get_class($this);
        $metadata = new ClassMetadata(new \ReflectionClass($this));

        $this->cache->getItem(\str_replace('\\', '_', $className))
            ->willReturn($item = $this->prophesize(ItemInterface::class));
        $item->set($metadata)
            ->willReturn($item)
            ->shouldBeCalled();
        $this->cache->save($item)->shouldBeCalled();

        $factory = new MetadataFactory($this->loader->reveal(), null, $this->cache->reveal());
        $factory->setMetadataFor($className, $metadata);
    }

    /**
     * @test
     */
    #[Test]
    public function get_metadata_for_should_load_data_from_cache_pool(): void
    {
        $className = \str_replace(['_', '\\'], ['__', '_'], \get_class($this));
        $metadata = new ClassMetadata(new \ReflectionClass($this));

        $this->cache = new ArrayAdapter();
        $item = $this->cache->getItem($className);
        $item->set($metadata);
        $this->cache->save($item);

        $this->loader->loadClassMetadata(Argument::type(ClassMetadataInterface::class))->shouldNotBeCalled();

        $factory = new MetadataFactory($this->loader->reveal(), null, $this->cache);
        $factory->getMetadataFor($this);
    }

    /**
     * @test
     */
    #[Test]
    public function get_metadata_for_should_store_data_into_cache(): void
    {
        $this->cache->getItem(Argument::type('string'))
            ->willReturn($item = $this->prophesize(ItemInterface::class));
        $item->isHit()->willReturn(false);
        $item->set(Argument::type(ClassMetadataInterface::class))
            ->willReturn($item)
            ->shouldBeCalled();
        $this->cache->save($item)->shouldBeCalled();

        $this->loader->loadClassMetadata(Argument::type(ClassMetadataInterface::class))->willReturn(true);

        $factory = new MetadataFactory($this->loader->reveal(), null, $this->cache->reveal());
        $factory->getMetadataFor($this);
    }

    /**
     * @test
     */
    #[Test]
    public function get_metadata_for_should_dispatch_loaded_event(): void
    {
        $this->loader->loadClassMetadata(Argument::type(ClassMetadataInterface::class))->willReturn(true);

        $that = $this;
        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(
            Argument::that(static function ($arg) use ($that) {
                return $arg instanceof ClassMetadataLoadedEvent && $arg->getMetadata()->getName() === \get_class($that);
            })
        )
            ->shouldBeCalled();
        $eventDispatcher->addMethodProphecy($eventDispatcher->dispatch(Argument::cetera()));

        $factory = new MetadataFactory($this->loader->reveal(), $eventDispatcher->reveal());
        $factory->getMetadataFor($this);
    }

    /**
     * @test
     */
    #[Test]
    public function set_metadata_class_should_check_class_existence(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $factory = new MetadataFactory($this->loader->reveal());
        $factory->setMetadataClass('NonExistentClass');
    }

    /**
     * @test
     */
    #[Test]
    public function set_metadata_class_should_check_class_implements_class_metadata_interface(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $factory = new MetadataFactory($this->loader->reveal());
        $factory->setMetadataClass(FakeClassNoMetadata::class);
    }

    /**
     * @test
     */
    #[Test]
    public function set_metadata_class_should_create_specified_object(): void
    {
        $this->loader->loadClassMetadata(Argument::type(ClassMetadataInterface::class))->willReturn(false);

        $factory = new MetadataFactory($this->loader->reveal());
        $factory->setMetadataClass(FakeClassMetadata::class);

        self::assertInstanceOf(FakeClassMetadata::class, $factory->getMetadataFor($this));
    }

    /**
     * @test
     */
    #[Test]
    public function get_metadata_for_should_call_metadata_finalizer_method(): void
    {
        $this->loader->loadClassMetadata(Argument::type(ClassMetadataInterface::class))->willReturn(true);

        $factory = new MetadataFactory($this->loader->reveal());
        $factory->setMetadataClass(FakeClassMetadata::class);
        $metadata = $factory->getMetadataFor($this);

        self::assertTrue($metadata->finalized);
    }

    /**
     * @test
     */
    #[Test]
    public function get_metadata_for_should_not_merge_with_superclasses_if_fails(): void
    {
        $this->loader->loadClassMetadata(Argument::type(ClassMetadataInterface::class))->willReturn(false);

        $metadata = $this->prophesize(ClassMetadataInterface::class);

        $metadata->merge(Argument::cetera())->shouldNotBeCalled();
        $metadata->getReflectionClass()->willReturn(new \ReflectionClass($this));

        $factory = new MockedClassMetadataFactory($this->loader->reveal());
        $factory->mock = $metadata->reveal();
        $factory->getMetadataFor($this);
    }
}
