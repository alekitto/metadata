<?php declare(strict_types=1);

namespace Kcs\Metadata\Tests\Fixtures\AnnotationProcessorLoader\Processor;

use Kcs\Metadata\Loader\Processor\Annotation\Processor;
use Kcs\Metadata\Loader\Processor\ProcessorInterface;
use Kcs\Metadata\MetadataInterface;
use Kcs\Metadata\Tests\Fixtures\AnnotationProcessorLoader\Annotation\ClassAnnot;

/**
 * @Processor(annotation=ClassAnnot::class)
 */
class ClassAnnotProcessor implements ProcessorInterface
{
    public function process(MetadataInterface $metadata, $subject): void
    {
    }
}
