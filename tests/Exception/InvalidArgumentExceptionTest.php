<?php declare(strict_types=1);

namespace Kcs\Metadata\Tests\Exception;

use Kcs\Metadata\Exception\InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class InvalidArgumentExceptionTest extends TestCase
{
    public static function messages_data_provider(): array
    {
        return [
            ['Class NonExistentTestClass does not exist. Cannot retrieve its metadata', InvalidArgumentException::CLASS_DOES_NOT_EXIST, 'NonExistentTestClass'],
            ['Unknown reason', 'Unknown reason'],
            ['Printed string', 'Printed %s', 'string'],
            ['Cannot create metadata for non-objects. Got: "integer"', InvalidArgumentException::VALUE_IS_NOT_AN_OBJECT, 2],
        ];
    }

    /**
     * @dataProvider messages_data_provider
     * @test
     */
    #[DataProvider('messages_data_provider')]
    #[Test]
    public function create_should_return_exception_with_valid_message(): void
    {
        $arguments = \func_get_args();
        $expected = \array_shift($arguments);

        /** @var InvalidArgumentException $ex */
        $ex = \call_user_func_array(InvalidArgumentException::class.'::create', $arguments);

        self::assertInstanceOf(InvalidArgumentException::class, $ex);
        self::assertEquals($expected, $ex->getMessage());
    }
}
