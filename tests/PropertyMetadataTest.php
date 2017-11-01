<?php declare(strict_types=1);

namespace Kcs\Metadata\Tests;

use Kcs\Metadata\PropertyMetadata;
use Kcs\Metadata\Tests\Fixtures\ClassForMetadata;
use PHPUnit\Framework\TestCase;

class VirtualPropertyMetadata extends PropertyMetadata
{
    public function getValue()
    {
        return 'FOO_BAR';
    }
}

class PropertyMetadataTest extends TestCase
{
    public function testShouldUnserializeVirtualProperties()
    {
        $property = new VirtualPropertyMetadata(ClassForMetadata::class, 'nonExistentAttribute');

        $unserialized = unserialize(serialize($property));
        $this->assertEquals('FOO_BAR', $unserialized->getValue());

        try {
            $unserialized->getReflection();
            $this->fail();
        } catch (\ReflectionException $e) {
        }
    }
}
