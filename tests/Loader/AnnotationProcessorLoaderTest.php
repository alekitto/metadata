<?php

namespace Kcs\Metadata\Tests\Loader;

use Doctrine\Common\Annotations\Reader;
use Kcs\Metadata\ClassMetadata;
use Kcs\Metadata\Loader\AnnotationProcessorLoader as BaseLoader;
use Kcs\Metadata\Loader\Processor\ProcessorFactoryInterface;
use Kcs\Metadata\MethodMetadata;
use Kcs\Metadata\PropertyMetadata;
use Kcs\Metadata\Tests\Fixtures\AnnotationProcessorLoader\Annotation\ClassAnnot;
use Kcs\Metadata\Tests\Fixtures\AnnotationProcessorLoader\Annotation\MethodAnnotation1;
use Kcs\Metadata\Tests\Fixtures\AnnotationProcessorLoader\Annotation\MethodAnnotation2;
use Kcs\Metadata\Tests\Fixtures\AnnotationProcessorLoader\Annotation\NotHandledAnnotation;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class AnnotationProcessorLoader extends BaseLoader
{
    protected function createMethodMetadata(\ReflectionMethod $reflectionMethod)
    {
        return new MethodMetadata($reflectionMethod->class, $reflectionMethod->name);
    }

    protected function createPropertyMetadata(\ReflectionProperty $reflectionProperty)
    {
        return new PropertyMetadata($reflectionProperty->class, $reflectionProperty->name);
    }
}

class AnnotationProcessorLoaderTest extends \PHPUnit_Framework_TestCase
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
        $this->reader = $this->prophesize('Doctrine\Common\Annotations\Reader');
        $this->processorFactory = $this->prophesize('Kcs\Metadata\Loader\Processor\ProcessorFactoryInterface');

        $this->loader = new AnnotationProcessorLoader($this->processorFactory->reveal());
        $this->loader->setReader($this->reader->reveal());
    }

    /**
     * @test
     */
    public function load_class_loads_metadata_correctly()
    {
        $reflClass = new \ReflectionClass('Kcs\Metadata\Tests\Fixtures\AnnotationProcessorLoader\SimpleObject');
        $metadata = new ClassMetadata($reflClass);

        $this->reader->getClassAnnotations($reflClass)
            ->willReturn([
                new ClassAnnot(),
                new NotHandledAnnotation()
            ]);
        $this->reader->getMethodAnnotations($reflClass->getMethod('getAuthor'))
            ->willReturn([
                new NotHandledAnnotation(),
                new MethodAnnotation1(),
                new MethodAnnotation2()
            ]);
        $this->reader->getPropertyAnnotations($reflClass->getProperty('createdAt'))
            ->willReturn([
                new NotHandledAnnotation()
            ]);
        $this->reader->getPropertyAnnotations($reflClass->getProperty('author'))
            ->willReturn([]);

        $this->processorFactory->getProcessor(Argument::type('Kcs\Metadata\Tests\Fixtures\AnnotationProcessorLoader\Annotation\NotHandledAnnotation'))
            ->willReturn();

        $classAnnotationProcessor = $this->prophesize('Kcs\Metadata\Loader\Processor\ProcessorInterface');
        $classAnnotationProcessor->process(Argument::type('Kcs\Metadata\MetadataInterface'), Argument::type('Kcs\Metadata\Tests\Fixtures\AnnotationProcessorLoader\Annotation\ClassAnnot'))
            ->shouldBeCalledTimes(1);
        $this->processorFactory->getProcessor(Argument::type('Kcs\Metadata\Tests\Fixtures\AnnotationProcessorLoader\Annotation\ClassAnnot'))
            ->willReturn($classAnnotationProcessor->reveal());

        $methodAnnotationProcessor = $this->prophesize('Kcs\Metadata\Loader\Processor\ProcessorInterface');
        $methodAnnotationProcessor->process(Argument::type('Kcs\Metadata\MetadataInterface'), Argument::type('Kcs\Metadata\Tests\Fixtures\AnnotationProcessorLoader\Annotation\MethodAnnotation1'))
            ->shouldBeCalledTimes(1);
        $methodAnnotationProcessor->process(Argument::type('Kcs\Metadata\MetadataInterface'), Argument::type('Kcs\Metadata\Tests\Fixtures\AnnotationProcessorLoader\Annotation\MethodAnnotation2'))
            ->shouldBeCalledTimes(1);
        $this->processorFactory->getProcessor(Argument::type('Kcs\Metadata\Tests\Fixtures\AnnotationProcessorLoader\Annotation\MethodAnnotation1'))
            ->willReturn($methodAnnotationProcessor->reveal());
        $this->processorFactory->getProcessor(Argument::type('Kcs\Metadata\Tests\Fixtures\AnnotationProcessorLoader\Annotation\MethodAnnotation2'))
            ->willReturn($methodAnnotationProcessor->reveal());

        $this->loader->loadClassMetadata($metadata);
    }
}
