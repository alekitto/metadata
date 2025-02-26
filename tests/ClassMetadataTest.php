<?php declare(strict_types=1);

namespace Kcs\Metadata\Tests;

use Kcs\Metadata\ClassMetadata;
use Kcs\Metadata\Exception\InvalidArgumentException;
use Kcs\Metadata\MethodMetadata;
use Kcs\Metadata\NullMetadata;
use Kcs\Metadata\PropertyMetadata;
use Kcs\Metadata\Tests\Fixtures\ClassForMetadata;
use Kcs\Metadata\Tests\Fixtures\MetadataClassWithAttributes;
use Kcs\Metadata\Tests\Fixtures\SubClassForMetadata;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ClassMetadataTest extends TestCase
{
    /**
     * Needed for tests.
     *
     * @var null
     */
    private $attr;

    /**
     * @test
     */
    #[Test]
    public function null_metadata_should_be_mergeable(): void
    {
        $metadata = new ClassMetadata(new \ReflectionClass($this));
        $metadata->merge(new NullMetadata(''));

        self::assertTrue(true);
    }

    /**
     * @test
     */
    #[Test]
    public function merge_with_metadata_of_wrong_class_should_throw(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $metadata = new ClassMetadata(new \ReflectionClass($this));
        $metadata->merge(new PropertyMetadata(\get_class($this), 'attr'));
    }

    /**
     * @test
     */
    #[Test]
    public function merge_with_metadata_out_of_class_hierarchy_should_throw(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $metadata = new ClassMetadata(new \ReflectionClass($this));
        $metadata->merge(new ClassMetadata(new \ReflectionClass('stdClass')));
    }

    /**
     * @test
     */
    #[Test]
    public function merge_should_merge_all_attributes(): void
    {
        $class_ = new ClassForMetadata();
        $subclass_ = new SubClassForMetadata();

        $metadata = new ClassMetadata(new \ReflectionClass($class_));
        $submetadata = new ClassMetadata(new \ReflectionClass($subclass_));

        $metadata->addAttributeMetadata(new PropertyMetadata(\get_class($class_), 'attributeFirst'));
        $metadata->addAttributeMetadata(new PropertyMetadata(\get_class($class_), 'attributeSecond'));
        $metadata->addAttributeMetadata(new MethodMetadata(\get_class($class_), 'methodOne'));

        $submetadata->addAttributeMetadata(new MethodMetadata(\get_class($subclass_), 'methodOne'));

        $submetadata->merge($metadata);

        $attributes = $submetadata->getAttributesMetadata();
        self::assertCount(3, $attributes);

        self::assertInstanceOf(PropertyMetadata::class, $submetadata->getAttributeMetadata('attributefirst'));
    }

    /**
     * @test
     */
    #[Test]
    public function serialize_of_metadata_class_should_keep_public_properties_and_attributes(): void
    {
        $class_ = new ClassForMetadata();

        $metadata = new MetadataClassWithAttributes(new \ReflectionClass($class_));
        $metadata->addAttributeMetadata(new PropertyMetadata(\get_class($class_), 'attributeFirst'));
        $metadata->attributeOne = 'ONE';
        $metadata->attributeTwo = 'TEST';

        $des = \unserialize(\serialize($metadata));

        self::assertInstanceOf(MetadataClassWithAttributes::class, $des);
        self::assertEquals('ONE', $des->attributeOne);
        self::assertEquals('TEST', $des->attributeTwo);
        self::assertCount(1, $des->getAttributesMetadata());
        self::assertInstanceOf(PropertyMetadata::class, $des->getAttributeMetadata('attributeFirst'));
    }

    /**
     * @test
     */
    #[Test]
    public function get_name_should_return_class_name(): void
    {
        $class_ = new ClassForMetadata();
        $metadata = new ClassMetadata(new \ReflectionClass($class_));

        self::assertEquals(\get_class($class_), $metadata->getName());
    }

    /**
     * @test
     */
    #[Test]
    public function get_name_should_reinit_reflection_upon_unserialization(): void
    {
        $class_ = new ClassForMetadata();
        $metadata = new ClassMetadata(new \ReflectionClass($class_));

        $serialized = \serialize($metadata);
        $metadata_unser = \unserialize($serialized);

        self::assertNotNull($metadata_unser->getReflectionClass());
    }

    /**
     * @test
     */
    #[Test]
    public function class_metadata_should_ignore_case(): void
    {
        $class_ = new ClassForMetadata();

        $metadata = new ClassMetadata(new \ReflectionClass($class_));

        $metadata->addAttributeMetadata(new PropertyMetadata(\get_class($class_), 'attributeFirst'));
        $metadata->addAttributeMetadata(new PropertyMetadata(\get_class($class_), 'attributeSecond'));
        $metadata->addAttributeMetadata(new MethodMetadata(\get_class($class_), 'methodOne'));

        self::assertInstanceOf(PropertyMetadata::class, $metadata->getAttributeMetadata('attributefirst'));
        self::assertInstanceOf(PropertyMetadata::class, $metadata->getAttributeMetadata('ATTRIBUTESECOND'));
        self::assertInstanceOf(MethodMetadata::class, $metadata->getAttributeMetadata('MethodOne'));
    }
}
