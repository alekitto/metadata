<?php

namespace Kcs\Metadata\Tests\Fixtures\AnnotationProcessorLoader;

use Kcs\Metadata\Tests\Fixtures\AnnotationProcessorLoader\Annotation;

/**
 * @Annotation\ClassAnnot
 */
class SimpleObject
{
    /**
     * @Annotation\NotHandledAnnotation
     */
    private $createdAt;

    private $author;

    /**
     * @Annotation\NotHandledAnnotation
     * @Annotation\MethodAnnotation1
     * @Annotation\MethodAnnotation2
     */
    public function getAuthor()
    {
        return $this->author;
    }
}
