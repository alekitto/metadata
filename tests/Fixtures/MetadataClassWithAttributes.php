<?php

namespace Kcs\Metadata\Tests\Fixtures;

use Kcs\Metadata\ClassMetadata;

class MetadataClassWithAttributes extends ClassMetadata
{
    public $attributeOne;

    public $attributeTwo;

    private $attributePrivate;
}
