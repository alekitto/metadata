<?php declare(strict_types=1);

namespace Kcs\Metadata\Tests\Loader\Locator;

use Kcs\Metadata\Loader\Locator\FileLocatorInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

abstract class BaseFileLocatorTests extends TestCase
{
    /**
     * @test
     */
    #[Test]
    public function locate_should_return_correct_results(): void
    {
        $baseDir = realpath(__DIR__ . '/../../Fixtures/Locator');
        $result = $this->getLocator()->locate($baseDir, '.yml');
        \sort($result);

        self::assertEquals([
            $baseDir.'/config/config_file.yml',
            $baseDir.'/src/AppBundle/config/configuration.yml',
        ], $result);
    }

    /**
     * @test
     */
    #[Test]
    public function locate_must_throw_if_extension_parameter_is_invalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->getLocator()->locate(__DIR__, 'base.yml');
    }

    /**
     * @return FileLocatorInterface
     */
    abstract protected function getLocator(): FileLocatorInterface;
}
