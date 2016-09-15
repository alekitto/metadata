<?php

namespace Kcs\Metadata\Tests;

use Kcs\Metadata\PropertyMetadata;
use Kcs\Metadata\Tests\Fixtures\ClassForMetadata;

class VirtualPropertyMetadata extends PropertyMetadata
{
    public function getValue()
    {
        return 'FOO_BAR';
    }
}

class PropertyMetadataTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldUnserializeVirtualProperties()
    {
        $property = new VirtualPropertyMetadata('Kcs\Metadata\Tests\Fixtures\ClassForMetadata', 'nonExistentAttribute');

        $unserialized = unserialize(serialize($property));
        $this->assertEquals('FOO_BAR', $unserialized->getValue());

        try {
            $unserialized->getReflection();
            $this->fail();
        } catch (\ReflectionException $e) {
        }
    }
}
