<?php declare(strict_types=1);

namespace Kcs\Metadata\Loader\Processor\Annotation;

use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation()
 * @Target({"CLASS"})
 */
class Processor
{
    /**
     * @var string
     *
     * @Required()
     */
    public $annotation;
}
