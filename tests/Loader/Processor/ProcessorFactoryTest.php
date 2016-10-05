<?php

namespace Kcs\Metadata\Tests\Loader\Processor;

use Kcs\Metadata\Loader\Processor\ProcessorFactory;
use Kcs\Metadata\Loader\Processor\ProcessorInterface;
use Kcs\Metadata\MetadataInterface;
use Kcs\Metadata\Tests\Fixtures\Annotation;

class FakeProcessor implements ProcessorInterface
{
    public function process(MetadataInterface $metadata, $subject)
    {
    }
}

class FakeProcessor2 implements ProcessorInterface
{
    public function process(MetadataInterface $metadata, $subject)
    {
    }
}

class FakeProcessor3 implements ProcessorInterface
{
    public function process(MetadataInterface $metadata, $subject)
    {
    }
}

class ProcessorFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException \Kcs\Metadata\Exception\InvalidArgumentException
     */
    public function register_processor_should_throw_if_invalid_class_is_passed()
    {
        $factory = new ProcessorFactory();
        $factory->registerProcessor('stdClass', 'stdClass');
    }

    /**
     * @test
     */
    public function get_processor_returns_the_same_instance_if_called_multiple_times()
    {
        $factory = new ProcessorFactory();
        $factory->registerProcessor('Kcs\Metadata\Tests\Fixtures\Annotation', 'Kcs\Metadata\Tests\Loader\Processor\FakeProcessor');

        $processor = $factory->getProcessor('Kcs\Metadata\Tests\Fixtures\Annotation');
        $this->assertSame($processor, $factory->getProcessor('Kcs\Metadata\Tests\Fixtures\Annotation'));
        $this->assertEquals(spl_object_hash($processor), spl_object_hash($factory->getProcessor('Kcs\Metadata\Tests\Fixtures\Annotation')));
    }

    /**
     * @test
     */
    public function get_processor_returns_correct_processor_if_object_is_passed()
    {
        $factory = new ProcessorFactory();
        $factory->registerProcessor('Kcs\Metadata\Tests\Fixtures\Annotation', 'Kcs\Metadata\Tests\Loader\Processor\FakeProcessor');

        $processor = $factory->getProcessor(new Annotation());
        $this->assertInstanceOf('Kcs\Metadata\Tests\Loader\Processor\FakeProcessor', $processor);
    }

    /**
     * @test
     */
    public function get_processor_returns_null_if_subject_is_unknown()
    {
        $factory = new ProcessorFactory();
        $factory->registerProcessor('stdClass', 'Kcs\Metadata\Tests\Loader\Processor\FakeProcessor');

        $this->assertNull($factory->getProcessor(new Annotation()));
    }

    /**
     * @test
     */
    public function get_processor_returns_composite_processor_if_more_than_one_processor_have_been_registered()
    {
        $factory = new ProcessorFactory();
        $factory->registerProcessor('Kcs\Metadata\Tests\Fixtures\Annotation', 'Kcs\Metadata\Tests\Loader\Processor\FakeProcessor');
        $factory->registerProcessor('Kcs\Metadata\Tests\Fixtures\Annotation', 'Kcs\Metadata\Tests\Loader\Processor\FakeProcessor2');
        $factory->registerProcessor('Kcs\Metadata\Tests\Fixtures\Annotation', 'Kcs\Metadata\Tests\Loader\Processor\FakeProcessor3');

        $this->assertInstanceOf('Kcs\Metadata\Loader\Processor\CompositeProcessor', $factory->getProcessor('Kcs\Metadata\Tests\Fixtures\Annotation'));
    }
}
