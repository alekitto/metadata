<?php declare(strict_types=1);

namespace Kcs\Metadata\Tests\Loader;

use Kcs\Metadata\ClassMetadata;
use Kcs\Metadata\Loader\AttributesProcessorLoader as BaseLoader;
use Kcs\Metadata\Loader\Processor\ProcessorFactoryInterface;
use Kcs\Metadata\Loader\Processor\ProcessorInterface;
use Kcs\Metadata\MetadataInterface;
use Kcs\Metadata\MethodMetadata;
use Kcs\Metadata\PropertyMetadata;
use Kcs\Metadata\Tests\Fixtures\AnnotationProcessorLoader\Annotation\ClassAnnot;
use Kcs\Metadata\Tests\Fixtures\AnnotationProcessorLoader\Annotation\MethodAnnotation1;
use Kcs\Metadata\Tests\Fixtures\AnnotationProcessorLoader\Annotation\MethodAnnotation2;
use Kcs\Metadata\Tests\Fixtures\AnnotationProcessorLoader\Annotation\NotHandledAnnotation;
use Kcs\Metadata\Tests\Fixtures\AnnotationProcessorLoader\SimpleObjectWithAttributes;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class AttributesProcessorLoader extends BaseLoader
{
    protected function createMethodMetadata(\ReflectionMethod $reflectionMethod): MetadataInterface
    {
        return new MethodMetadata($reflectionMethod->class, $reflectionMethod->name);
    }

    protected function createPropertyMetadata(\ReflectionProperty $reflectionProperty): MetadataInterface
    {
        return new PropertyMetadata($reflectionProperty->class, $reflectionProperty->name);
    }
}

/**
 * @requires PHP >= 8.0
 */
class AttributesProcessorLoaderTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ProcessorFactoryInterface|ObjectProphecy
     */
    private ObjectProphecy $processorFactory;
    private AttributesProcessorLoader $loader;

    protected function setUp(): void
    {
        $this->processorFactory = $this->prophesize(ProcessorFactoryInterface::class);
        $this->loader = new AttributesProcessorLoader($this->processorFactory->reveal());
    }

    /**
     * @test
     */
    public function load_class_loads_metadata_correctly()
    {
        $reflClass = new \ReflectionClass(SimpleObjectWithAttributes::class);
        $metadata = new ClassMetadata($reflClass);

        $this->processorFactory->getProcessor(Argument::type(NotHandledAnnotation::class))
            ->willReturn();

        $classAnnotationProcessor = $this->prophesize(ProcessorInterface::class);
        $classAnnotationProcessor->process(Argument::type(MetadataInterface::class), Argument::type(ClassAnnot::class))
            ->shouldBeCalledTimes(1);
        $this->processorFactory->getProcessor(Argument::type(ClassAnnot::class))
            ->willReturn($classAnnotationProcessor->reveal());

        $methodAnnotationProcessor = $this->prophesize(ProcessorInterface::class);
        $methodAnnotationProcessor->process(Argument::type(MetadataInterface::class), Argument::type(MethodAnnotation1::class))
            ->shouldBeCalledTimes(1);
        $methodAnnotationProcessor->process(Argument::type(MetadataInterface::class), Argument::type(MethodAnnotation2::class))
            ->shouldBeCalledTimes(1);
        $this->processorFactory->getProcessor(Argument::type(MethodAnnotation1::class))
            ->willReturn($methodAnnotationProcessor->reveal());
        $this->processorFactory->getProcessor(Argument::type(MethodAnnotation2::class))
            ->willReturn($methodAnnotationProcessor->reveal());

        $this->loader->loadClassMetadata($metadata);
    }
}
