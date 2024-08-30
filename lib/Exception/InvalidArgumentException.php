<?php

declare(strict_types=1);

namespace Kcs\Metadata\Exception;

use Throwable;

use function func_get_args;
use function get_class;
use function gettype;
use function is_object;
use function Safe\sprintf;

class InvalidArgumentException extends \InvalidArgumentException
{
    public const CLASS_DOES_NOT_EXIST = 1;
    public const VALUE_IS_NOT_AN_OBJECT = 2;
    public const NOT_MERGEABLE_METADATA = 3;
    public const INVALID_METADATA_CLASS = 4;
    public const INVALID_PROCESSOR_INTERFACE_CLASS = 5;

    /**
     * {@inheritDoc}
     */
    final public function __construct(string $message = '', int $code = 0, Throwable|null $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Create a new instance of InvalidArgumentException with meaningful message.
     */
    public static function create(mixed $reason): self
    {
        $arguments = func_get_args();

        switch ($reason) {
            case self::CLASS_DOES_NOT_EXIST:
                $message = sprintf('Class %s does not exist. Cannot retrieve its metadata', $arguments[1]);

                return new static($message);

            case self::VALUE_IS_NOT_AN_OBJECT:
                $message = sprintf('Cannot create metadata for non-objects. Got: "%s"', gettype($arguments[1]));

                return new static($message);

            case self::NOT_MERGEABLE_METADATA:
                $message = sprintf(
                    'Cannot merge metadata of class "%s" with "%s"',
                    is_object($arguments[2]) ? get_class($arguments[2]) : $arguments[2],
                    is_object($arguments[1]) ? get_class($arguments[1]) : $arguments[1],
                );

                return new static($message);

            case self::INVALID_METADATA_CLASS:
                $message = sprintf(
                    '"%s" is not a valid metadata object class',
                    is_object($arguments[1]) ? get_class($arguments[1]) : $arguments[1],
                );

                return new static($message);

            case self::INVALID_PROCESSOR_INTERFACE_CLASS:
                $message = sprintf(
                    '"%s" is not a valid ProcessorInterface class',
                    $arguments[1],
                );

                return new static($message);
        }

        return new static(sprintf(...$arguments));
    }
}
