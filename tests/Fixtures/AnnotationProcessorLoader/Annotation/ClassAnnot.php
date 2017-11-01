<?php declare(strict_types=1);

namespace Kcs\Metadata\Tests\Fixtures\AnnotationProcessorLoader\Annotation;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class ClassAnnot
{
    public $property = false;
}
