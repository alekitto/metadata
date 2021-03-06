<?php declare(strict_types=1);

namespace Kcs\Metadata\Tests\Loader\Locator;

use Kcs\Metadata\Loader\Locator\FileLocatorInterface;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

abstract class BaseFileLocatorTest extends TestCase
{
    /**
     * @test
     */
    public function locate_should_return_correct_results(): void
    {
        vfsStream::setup();
        $root = vfsStream::create([
            'config' => [
                'config_file.yml' => 'CONTENT',
                'config_file.php' => 'CONTENT',
                'foo.txt' => 'CONTENT',
            ],
            'src' => [
                'AppBundle' => [
                    'config' => [
                        'configuration.yml' => 'CONTENT',
                    ],
                ],
            ],
            'web' => [
                'app.php' => 'CONTENT',
            ],
        ]);

        $result = $this->getLocator()->locate($root->url(), '.yml');
        \sort($result);

        self::assertEquals([
            $root->url().'/config/config_file.yml',
            $root->url().'/src/AppBundle/config/configuration.yml',
        ], $result);
    }

    /**
     * @test
     */
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
