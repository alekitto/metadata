<?php

namespace Kcs\Metadata\Tests\Loader\Locator;

use Kcs\Metadata\Loader\Locator\FileLocatorInterface;
use org\bovigo\vfs\vfsStream;

abstract class BaseFileLocatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function locate_should_return_correct_results()
    {
        vfsStream::setup();
        $root = vfsStream::create([
            'config' => [
                'config_file.yml' => 'CONTENT',
                'config_file.php' => 'CONTENT',
                'foo.txt' => 'CONTENT'
            ],
            'src' => [
                'AppBundle' => [
                    'config' => [
                        'configuration.yml' => 'CONTENT'
                    ]
                ]
            ],
            'web' => [
                'app.php' => 'CONTENT'
            ]
        ]);

        $result = $this->getLocator()->locate($root->url(), '*.yml');
        sort($result);

        $this->assertEquals([
            $root->url() . '/config/config_file.yml',
            $root->url() . '/src/AppBundle/config/configuration.yml'
        ], $result);
    }

    /**
     * @return FileLocatorInterface
     */
    abstract protected function getLocator();
}
