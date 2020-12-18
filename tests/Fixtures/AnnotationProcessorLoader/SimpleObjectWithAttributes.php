<?php declare(strict_types=1);

namespace Kcs\Metadata\Tests\Fixtures\AnnotationProcessorLoader;

#[Annotation\ClassAnnot]
class SimpleObjectWithAttributes
{
    #[Annotation\NotHandledAnnotation]
    private $createdAt;

    private $author;

    #[
        Annotation\NotHandledAnnotation,
        Annotation\MethodAnnotation1,
        Annotation\MethodAnnotation2,
    ]
    public function getAuthor()
    {
        return $this->author;
    }
}
