<?php declare(strict_types=1);

namespace Kcs\Metadata\Tests\Loader\Processor;

use Kcs\Metadata\Loader\Processor\CompositeProcessor;
use Kcs\Metadata\Loader\Processor\ProcessorFactory;
use Kcs\Metadata\Loader\Processor\ProcessorInterface;
use Kcs\Metadata\MetadataInterface;
use Kcs\Metadata\Tests\Fixtures\Annotation;
use PHPUnit\Framework\TestCase;

class FakeProcessor implements ProcessorInterface
{
    public function process(MetadataInterface $metadata, $subject): void
    {
    }
}

class FakeProcessor2 implements ProcessorInterface
{
    public function process(MetadataInterface $metadata, $subject): void
    {
    }
}

class FakeProcessor3 implements ProcessorInterface
{
    public function process(MetadataInterface $metadata, $subject): void
    {
    }
}

class ProcessorFactoryTest extends TestCase
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
        $factory->registerProcessor(Annotation::class, FakeProcessor::class);

        $processor = $factory->getProcessor(Annotation::class);
        $this->assertSame($processor, $factory->getProcessor(Annotation::class));
        $this->assertEquals(spl_object_hash($processor), spl_object_hash($factory->getProcessor(Annotation::class)));
    }

    /**
     * @test
     */
    public function get_processor_returns_correct_processor_if_object_is_passed()
    {
        $factory = new ProcessorFactory();
        $factory->registerProcessor(Annotation::class, FakeProcessor::class);

        $processor = $factory->getProcessor(new Annotation());
        $this->assertInstanceOf(FakeProcessor::class, $processor);
    }

    /**
     * @test
     */
    public function get_processor_returns_null_if_subject_is_unknown()
    {
        $factory = new ProcessorFactory();
        $factory->registerProcessor('stdClass', FakeProcessor::class);

        $this->assertNull($factory->getProcessor(new Annotation()));
    }

    /**
     * @test
     */
    public function get_processor_returns_composite_processor_if_more_than_one_processor_have_been_registered()
    {
        $factory = new ProcessorFactory();
        $factory->registerProcessor(Annotation::class, FakeProcessor::class);
        $factory->registerProcessor(Annotation::class, FakeProcessor2::class);
        $factory->registerProcessor(Annotation::class, FakeProcessor3::class);

        $this->assertInstanceOf(CompositeProcessor::class, $factory->getProcessor(Annotation::class));
    }
}
