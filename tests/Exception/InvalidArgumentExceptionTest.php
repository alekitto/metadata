<?php

namespace Kcs\Metadata\Tests\Exception;

use Kcs\Metadata\Exception\InvalidArgumentException;

class InvalidArgumentExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function messages_data_provider()
    {
        return array(
            array('Class NonExistentTestClass does not exist. Cannot retrieve its metadata', InvalidArgumentException::CLASS_DOES_NOT_EXIST, 'NonExistentTestClass'),
            array('Unknown reason', 'Unknown reason'),
            array('Printed string', 'Printed %s', 'string'),
            array('Cannot create metadata for non-objects. Got: "integer"', InvalidArgumentException::VALUE_IS_NOT_AN_OBJECT, 2)
        );
    }

    /**
     * @dataProvider messages_data_provider
     * @test
     */
    public function create_should_return_exception_with_valid_message()
    {
        $arguments = func_get_args();
        $expected = array_shift($arguments);

        /** @var InvalidArgumentException $ex */
        $ex = call_user_func_array('Kcs\Metadata\Exception\InvalidArgumentException::create', $arguments);

        $this->assertInstanceOf('Kcs\Metadata\Exception\InvalidArgumentException', $ex);
        $this->assertEquals($expected, $ex->getMessage());
    }
}
