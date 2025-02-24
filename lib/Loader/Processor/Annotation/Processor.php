<?php

declare(strict_types=1);

namespace Kcs\Metadata\Loader\Processor\Annotation;

use Attribute;
use Doctrine\Common\Annotations\Annotation\Required;
use TypeError;

use function get_debug_type;
use function is_array;
use function is_string;
use function sprintf;

/**
 * @Annotation()
 * @Target({"CLASS"})
 */
#[Attribute]
class Processor
{
    /**
     * @phpstan-var class-string
     * @Required()
     */
    public string $annotation;

    /**
     * @param string|array $annotation Doctrine annotations would pass an array with all data.
     * @phpstan-param class-string|array $annotation
     */
    public function __construct(string|array $annotation = [])
    {
        if (is_string($annotation)) {
            $annotation = ['annotation' => $annotation];
        } elseif (! is_array($annotation)) {
            throw new TypeError(sprintf('"%s": Argument $data is expected to be a string or array, got "%s".', __METHOD__, get_debug_type($annotation)));
        }

        $this->annotation = $annotation['annotation'];
    }
}
