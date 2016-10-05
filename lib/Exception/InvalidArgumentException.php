<?php

namespace Kcs\Metadata\Exception;

class InvalidArgumentException extends \InvalidArgumentException
{
    const CLASS_DOES_NOT_EXIST = 1;
    const VALUE_IS_NOT_AN_OBJECT = 2;
    const NOT_MERGEABLE_METADATA = 3;
    const INVALID_METADATA_CLASS = 4;
    const INVALID_PROCESSOR_INTERFACE_CLASS = 5;

    /**
     * Create a new instance of InvalidArgumentException with meaningful message
     *
     * @param $reason
     *
     * @return static
     */
    public static function create($reason)
    {
        $arguments = func_get_args();

        switch ($reason) {
            case static::CLASS_DOES_NOT_EXIST:
                $message = sprintf('Class %s does not exist. Cannot retrieve its metadata', $arguments[1]);

                return new static($message);

            case static::VALUE_IS_NOT_AN_OBJECT:
                $message = sprintf('Cannot create metadata for non-objects. Got: "%s"', gettype($arguments[1]));

                return new static($message);

            case static::NOT_MERGEABLE_METADATA:
                $message = sprintf(
                    'Cannot merge metadata of class "%s" with "%s"',
                    is_object($arguments[2]) ? get_class($arguments[2]) : $arguments[2],
                    is_object($arguments[1]) ? get_class($arguments[1]) : $arguments[1]
                );

                return new static($message);

            case static::INVALID_METADATA_CLASS:
                $message = sprintf(
                    '"%s" is not a valid metadata object class',
                    is_object($arguments[1]) ? get_class($arguments[1]) : $arguments[1]
                );

                return new static($message);

            case static::INVALID_PROCESSOR_INTERFACE_CLASS:
                $message = sprintf(
                    '"%s" is not a valid ProcessorInterface class',
                    $arguments[1]
                );

                return new static($message);
        }

        return new static(call_user_func_array('sprintf', $arguments));
    }
}
