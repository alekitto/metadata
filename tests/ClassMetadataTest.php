<?php

namespace Kcs\Metadata\Tests;

use Kcs\Metadata\ClassMetadata;
use Kcs\Metadata\MethodMetadata;
use Kcs\Metadata\NullMetadata;
use Kcs\Metadata\PropertyMetadata;
use Kcs\Metadata\Tests\Fixtures\ClassForMetadata;
use Kcs\Metadata\Tests\Fixtures\MetadataClassWithAttributes;
use Kcs\Metadata\Tests\Fixtures\SubClassForMetadata;

class ClassMetadataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Needed for tests
     * @var null
     */
    private $attr;

    /**
     * @test
     */
    public function null_metadata_should_be_mergeable()
    {
        $metadata = new ClassMetadata(new \ReflectionClass($this));
        $metadata->merge(new NullMetadata(''));

        $this->assertTrue(true);
    }

    /**
     * @test
     * @expectedException \Kcs\Metadata\Exception\InvalidArgumentException
     */
    public function merge_with_metadata_of_wrong_clas_should_throw()
    {
        $metadata = new ClassMetadata(new \ReflectionClass($this));
        $metadata->merge(new PropertyMetadata($this, 'attr'));
    }

    /**
     * @test
     * @expectedException \Kcs\Metadata\Exception\InvalidArgumentException
     */
    public function merge_with_metadata_out_of_class_hierarchy_should_throw()
    {
        $metadata = new ClassMetadata(new \ReflectionClass($this));
        $metadata->merge(new ClassMetadata(new \ReflectionClass('stdClass')));
    }

    /**
     * @test
     */
    public function merge_should_merge_all_attributes()
    {
        $class_ = new ClassForMetadata();
        $subclass_ = new SubClassForMetadata();

        $metadata = new ClassMetadata(new \ReflectionClass($class_));
        $submetadata = new ClassMetadata(new \ReflectionClass($subclass_));

        $metadata->addAttributeMetadata(new PropertyMetadata(get_class($class_), 'attributeFirst'));
        $metadata->addAttributeMetadata(new PropertyMetadata(get_class($class_), 'attributeSecond'));
        $metadata->addAttributeMetadata(new MethodMetadata(get_class($class_), 'methodOne'));

        $submetadata->addAttributeMetadata(new MethodMetadata(get_class($subclass_), 'methodOne'));

        $submetadata->merge($metadata);

        $attributes = $submetadata->getAttributesMetadata();
        $this->assertCount(3, $attributes);
    }

    /**
     * @test
     */
    public function serialize_of_metadata_class_should_keep_public_properties_and_attributes()
    {
        $class_ = new ClassForMetadata();

        $metadata = new MetadataClassWithAttributes(new \ReflectionClass($class_));
        $metadata->addAttributeMetadata(new PropertyMetadata(get_class($class_), 'attributeFirst'));
        $metadata->attributeOne = 'ONE';
        $metadata->attributeTwo = 'TEST';

        $des = unserialize(serialize($metadata));

        $this->assertInstanceOf('Kcs\Metadata\Tests\Fixtures\MetadataClassWithAttributes', $des);
        $this->assertEquals('ONE', $des->attributeOne);
        $this->assertEquals('TEST', $des->attributeTwo);
        $this->assertCount(1, $des->getAttributesMetadata());
        $this->assertInstanceOf('Kcs\Metadata\PropertyMetadata', $des->getAttributeMetadata('attributeFirst'));
    }

    /**
     * @test
     */
    public function get_name_should_return_class_name()
    {
        $class_ = new ClassForMetadata();
        $metadata = new ClassMetadata(new \ReflectionClass($class_));

        $this->assertEquals(get_class($class_), $metadata->getName());
    }

    /**
     * @test
     */
    public function get_name_should_reinit_reflection_upon_unserialization()
    {
        $class_ = new ClassForMetadata();
        $metadata = new ClassMetadata(new \ReflectionClass($class_));

        $serialized = serialize($metadata);
        $metadata_unser = unserialize($serialized);

        $this->assertNotNull($metadata_unser->getReflectionClass());
    }
}
