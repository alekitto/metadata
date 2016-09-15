<?php

namespace Kcs\Metadata\Tests\Loader;

use Kcs\Metadata\Loader\FilesLoader;
use Prophecy\Argument;

class FilesLoaderTestLoader extends FilesLoader
{
    private $loader;

    public function __construct(array $paths, $loader)
    {
        $this->loader = $loader;
        parent::__construct($paths);
    }

    protected function getLoader($path)
    {
        return $this->loader;
    }
}

class FilesLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function loader_should_be_called_exact_times()
    {
        $fileLoader = $this->prophesize('Kcs\Metadata\Loader\LoaderInterface');
        $fileLoader->loadClassMetadata(Argument::cetera())->shouldBeCalledTimes(3);

        $loader = new FilesLoaderTestLoader([
            'test1.yml',
            'test2.yml',
            'test3.yml',
        ], $fileLoader->reveal());

        $loader->loadClassMetadata($this->prophesize('Kcs\Metadata\ClassMetadata')->reveal());
    }
}
