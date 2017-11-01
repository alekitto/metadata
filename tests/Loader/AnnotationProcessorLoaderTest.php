<?php declare(strict_types=1);

namespace Kcs\Metadata\Tests\Loader;

use Doctrine\Common\Annotations\Reader;
use Kcs\Metadata\ClassMetadata;
use Kcs\Metadata\Loader\AnnotationProcessorLoader as BaseLoader;
use Kcs\Metadata\Loader\Processor\ProcessorFactoryInterface;
use Kcs\Metadata\Loader\Processor\ProcessorInterface;
use Kcs\Metadata\MetadataInterface;
use Kcs\Metadata\MethodMetadata;
use Kcs\Metadata\PropertyMetadata;
use Kcs\Metadata\Tests\Fixtures\AnnotationProcessorLoader\Annotation\ClassAnnot;
use Kcs\Metadata\Tests\Fixtures\AnnotationProcessorLoader\Annotation\MethodAnnotation1;
use Kcs\Metadata\Tests\Fixtures\AnnotationProcessorLoader\Annotation\MethodAnnotation2;
use Kcs\Metadata\Tests\Fixtures\AnnotationProcessorLoader\Annotation\NotHandledAnnotation;
use Kcs\Metadata\Tests\Fixtures\AnnotationProcessorLoader\SimpleObject;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class AnnotationProcessorLoader extends BaseLoader
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

class AnnotationProcessorLoaderTest extends TestCase
{
    /**
     * @var Reader|ObjectProphecy
     */
    private $reader;

    /**
     * @var ProcessorFactoryInterface|ObjectProphecy
     */
    private $processorFactory;

    /**
     * @var AnnotationProcessorLoader
     */
    private $loader;

    protected function setUp()
    {
        $this->reader = $this->prophesize(Reader::class);
        $this->processorFactory = $this->prophesize(ProcessorFactoryInterface::class);

        $this->loader = new AnnotationProcessorLoader($this->processorFactory->reveal());
        $this->loader->setReader($this->reader->reveal());
    }

    /**
     * @test
     */
    public function load_class_loads_metadata_correctly()
    {
        $reflClass = new \ReflectionClass(SimpleObject::class);
        $metadata = new ClassMetadata($reflClass);

        $this->reader->getClassAnnotations($reflClass)
            ->willReturn([
                new ClassAnnot(),
                new NotHandledAnnotation(),
            ]);
        $this->reader->getMethodAnnotations($reflClass->getMethod('getAuthor'))
            ->willReturn([
                new NotHandledAnnotation(),
                new MethodAnnotation1(),
                new MethodAnnotation2(),
            ]);
        $this->reader->getPropertyAnnotations($reflClass->getProperty('createdAt'))
            ->willReturn([
                new NotHandledAnnotation(),
            ]);
        $this->reader->getPropertyAnnotations($reflClass->getProperty('author'))
            ->willReturn([]);

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
