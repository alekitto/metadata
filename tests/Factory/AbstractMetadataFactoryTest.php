<?php

namespace Kcs\Metadata\Tests\Factory;

use Doctrine\Common\Cache\Cache;
use Kcs\Metadata\ClassMetadata;
use Kcs\Metadata\ClassMetadataInterface;
use Kcs\Metadata\Event\ClassMetadataLoadedEvent;
use Kcs\Metadata\Factory\AbstractMetadataFactory;
use Kcs\Metadata\Loader\LoaderInterface;
use Prophecy\Argument;

class Factory extends AbstractMetadataFactory
{
    /**
     * @inheritDoc
     */
    protected function createMetadata(\ReflectionClass $class)
    {
        return new ClassMetadata($class);
    }
}

/**
 * @property LoaderInterface loader
 * @property Cache cache
 */
class AbstractMetadataFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->loader = $this->prophesize('Kcs\Metadata\Loader\LoaderInterface');
        $this->cache = $this->prophesize('Doctrine\Common\Cache\Cache');
    }

    /**
     * @test
     */
    public function has_metadata_should_return_false_on_non_existent_classes()
    {
        $factory = new Factory($this->loader->reveal());
        $this->assertFalse($factory->hasMetadataFor('NonExistentClass'));
    }

    /**
     * @test
     */
    public function has_metadata_should_return_false_on_invalid_value()
    {
        $factory = new Factory($this->loader->reveal());
        $this->assertFalse($factory->hasMetadataFor(array()));
    }

    public function invalid_value_provider()
    {
        return array(
            array([]),
            array('NonExistentClass')
        );
    }

    /**
     * @test
     * @expectedException \Kcs\Metadata\Exception\InvalidArgumentException
     * @dataProvider invalid_value_provider
     */
    public function get_metadata_should_throw_if_invalid_value_has_passed($value)
    {
        $factory = new Factory($this->loader->reveal());
        $factory->getMetadataFor($value);
    }

    /**
     * @test
     */
    public function get_metadata_for_should_call_the_metadata_loader()
    {
        $this->loader->loadClassMetadata(Argument::type('Kcs\Metadata\ClassMetadataInterface'))
            ->shouldBeCalled();

        $factory = new Factory($this->loader->reveal());
        $factory->getMetadataFor($this);
    }

    /**
     * @test
     */
    public function get_metadata_for_should_not_call_the_metadata_loader_twice()
    {
        $className = get_class($this);
        $this->loader->loadClassMetadata(Argument::that(function (ClassMetadataInterface $metadata) use ($className) {
            return $metadata->getReflectionClass()->name === $className;
        }))->shouldBeCalledTimes(1);

        $this->loader->addMethodProphecy(
            $this->loader->loadClassMetadata(Argument::type('Kcs\Metadata\ClassMetadataInterface'))
        );

        $factory = new Factory($this->loader->reveal());
        $factory->getMetadataFor($this);
        $factory->getMetadataFor($this);
    }

    /**
     * @test
     */
    public function get_metadata_for_should_load_data_from_cache()
    {
        $className = get_class($this);
        $metadata = new ClassMetadata(new \ReflectionClass($this));

        $this->cache->fetch($className)->willReturn($metadata);
        $this->loader->loadClassMetadata(Argument::type('Kcs\Metadata\ClassMetadataInterface'))->shouldNotBeCalled();

        $factory = new Factory($this->loader->reveal(), null, $this->cache->reveal());
        $factory->getMetadataFor($this);
    }

    /**
     * @test
     */
    public function get_metadata_for_should_store_data_into_cache()
    {
        $className = get_class($this);

        $this->cache->fetch(Argument::type('string'))->willReturn(null);
        $this->cache->save($className, Argument::type('Kcs\Metadata\ClassMetadataInterface'))
            ->shouldBeCalled()
            ->willReturn(true);
        $this->cache->save(Argument::type('string'), Argument::type('Kcs\Metadata\MetadataInterface'))->willReturn(true);

        $this->loader->addMethodProphecy(
            $this->loader->loadClassMetadata(Argument::type('Kcs\Metadata\ClassMetadataInterface'))
        );

        $factory = new Factory($this->loader->reveal(), null, $this->cache->reveal());
        $factory->getMetadataFor($this);
    }

    /**
     * @test
     */
    public function get_metadata_for_should_dispatch_loaded_event()
    {
        $this->loader->addMethodProphecy(
            $this->loader->loadClassMetadata(Argument::type('Kcs\Metadata\ClassMetadataInterface'))
        );

        $that = $this;
        $eventDispatcher = $this->prophesize('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $eventDispatcher->dispatch(
            'kcs_metadata.metadata_loaded',
            Argument::that(function($arg) use ($that) {
                return $arg instanceof ClassMetadataLoadedEvent && $arg->getMetadata()->getName() === get_class($that);
            })
        )
            ->shouldBeCalled();
        $eventDispatcher->addMethodProphecy($eventDispatcher->dispatch(Argument::cetera()));

        $factory = new Factory($this->loader->reveal(), $eventDispatcher->reveal());
        $factory->getMetadataFor($this);
    }
}
