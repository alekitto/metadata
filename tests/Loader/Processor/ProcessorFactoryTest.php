<?php declare(strict_types=1);

namespace Kcs\Metadata\Tests\Loader\Processor;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Kcs\Metadata\Exception\InvalidArgumentException;
use Kcs\Metadata\Loader\Processor\CompositeProcessor;
use Kcs\Metadata\Loader\Processor\ProcessorFactory;
use Kcs\Metadata\Loader\Processor\ProcessorInterface;
use Kcs\Metadata\MetadataInterface;
use Kcs\Metadata\Tests\Fixtures\Annotation;
use Kcs\Metadata\Tests\Fixtures\AnnotationProcessorLoader\Annotation\ClassAnnot;
use Kcs\Metadata\Tests\Fixtures\AnnotationProcessorLoader\Annotation\ClassAttrib;
use Kcs\Metadata\Tests\Fixtures\AnnotationProcessorLoader\Processor\ClassAnnotProcessor;
use Kcs\Metadata\Tests\Fixtures\AnnotationProcessorLoader\Processor\ClassAttribProcessor;
use PHPUnit\Framework\TestCase;

AnnotationRegistry::registerLoader('class_exists');

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
     */
    public function register_processor_should_throw_if_invalid_class_is_passed(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $factory = new ProcessorFactory();
        $factory->registerProcessor('stdClass', 'stdClass');
    }

    /**
     * @test
     */
    public function get_processor_returns_the_same_instance_if_called_multiple_times(): void
    {
        $factory = new ProcessorFactory();
        $factory->registerProcessor(Annotation::class, FakeProcessor::class);

        $processor = $factory->getProcessor(Annotation::class);
        self::assertSame($processor, $factory->getProcessor(Annotation::class));
        self::assertEquals(\spl_object_hash($processor), \spl_object_hash($factory->getProcessor(Annotation::class)));
    }

    /**
     * @test
     */
    public function get_processor_returns_correct_processor_if_object_is_passed(): void
    {
        $factory = new ProcessorFactory();
        $factory->registerProcessor(Annotation::class, FakeProcessor::class);

        $processor = $factory->getProcessor(new Annotation());
        self::assertInstanceOf(FakeProcessor::class, $processor);
    }

    /**
     * @test
     */
    public function get_processor_returns_null_if_subject_is_unknown(): void
    {
        $factory = new ProcessorFactory();
        $factory->registerProcessor('stdClass', FakeProcessor::class);

        self::assertNull($factory->getProcessor(new Annotation()));
    }

    /**
     * @test
     */
    public function get_processor_returns_composite_processor_if_more_than_one_processor_have_been_registered(): void
    {
        $factory = new ProcessorFactory();
        $factory->registerProcessor(Annotation::class, FakeProcessor::class);
        $factory->registerProcessor(Annotation::class, FakeProcessor2::class);
        $factory->registerProcessor(Annotation::class, FakeProcessor3::class);

        self::assertInstanceOf(CompositeProcessor::class, $factory->getProcessor(Annotation::class));
    }

    /**
     * @test
     */
    public function register_processors_should_find_and_register_annotated_processors(): void
    {
        $factory = new ProcessorFactory();
        $factory->registerProcessors(__DIR__.'/../../Fixtures/AnnotationProcessorLoader/Processor');

        self::assertInstanceOf(ClassAnnotProcessor::class, $factory->getProcessor(ClassAnnot::class));

        if (PHP_VERSION_ID >= 80000) {
            self::assertInstanceOf(ClassAttribProcessor::class, $factory->getProcessor(ClassAttrib::class));
        }
    }
}
