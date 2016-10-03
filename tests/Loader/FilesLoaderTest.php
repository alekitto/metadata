<?php

namespace Kcs\Metadata\Tests\Loader;

use Kcs\Metadata\ClassMetadataInterface;
use Kcs\Metadata\Loader\FileLoader;
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

class FileLoaderTestFileLoader extends FileLoader
{
    public static $called = false;

    public function loadClassMetadata(ClassMetadataInterface $classMetadata)
    {
        self::$called = true;
    }

    protected function loadClassMetadataFromFile($file_content, ClassMetadataInterface $classMetadata)
    {
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

    /**
     * @test
     * @expectedException \Kcs\Metadata\Exception\RuntimeException
     */
    public function loader_should_throw_if_no_loader_class_has_passed()
    {
        $loader = new FilesLoader([
            'test1.yml',
            'test2.yml',
            'test3.yml',
        ]);

        $loader->loadClassMetadata($this->prophesize('Kcs\Metadata\ClassMetadata')->reveal());
    }

    /**
     * @test
     */
    public function loader_should_call_correct_loader_class()
    {
        FileLoaderTestFileLoader::$called = false;
        $loader = new FilesLoader(['test1.yml'], 'Kcs\Metadata\Tests\Loader\FileLoaderTestFileLoader');
        $loader->loadClassMetadata($this->prophesize('Kcs\Metadata\ClassMetadata')->reveal());

        $this->assertTrue(FileLoaderTestFileLoader::$called);
    }
}
