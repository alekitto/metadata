<?php declare(strict_types=1);

namespace Kcs\Metadata\Tests\Loader;

use Kcs\Metadata\ClassMetadata;
use Kcs\Metadata\ClassMetadataInterface;
use Kcs\Metadata\Exception\RuntimeException;
use Kcs\Metadata\Loader\FileLoader;
use Kcs\Metadata\Loader\FilesLoader;
use Kcs\Metadata\Loader\LoaderInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class FilesLoaderTestLoader extends FilesLoader
{
    private $loader;

    public function __construct(array $paths, $loader)
    {
        $this->loader = $loader;
        parent::__construct($paths);
    }

    protected function getLoader(string $path): LoaderInterface
    {
        return $this->loader;
    }
}

class FileLoaderTestFileLoader extends FileLoader
{
    public static $called = false;

    public function loadClassMetadata(ClassMetadataInterface $classMetadata): bool
    {
        self::$called = true;

        return true;
    }

    protected function loadClassMetadataFromFile(string $file_content, ClassMetadataInterface $classMetadata): bool
    {
        return true;
    }
}

class FilesLoaderTest extends TestCase
{
    /**
     * @test
     */
    public function loader_should_be_called_exact_times()
    {
        $fileLoader = $this->prophesize(LoaderInterface::class);
        $fileLoader->loadClassMetadata(Argument::cetera())->shouldBeCalledTimes(3);

        $loader = new FilesLoaderTestLoader([
            'test1.yml',
            'test2.yml',
            'test3.yml',
        ], $fileLoader->reveal());

        $loader->loadClassMetadata($this->prophesize(ClassMetadata::class)->reveal());
    }

    /**
     * @test
     */
    public function loader_should_throw_if_no_loader_class_has_passed()
    {
        $this->expectException(RuntimeException::class);
        $loader = new FilesLoader([
            'test1.yml',
            'test2.yml',
            'test3.yml',
        ]);

        $loader->loadClassMetadata($this->prophesize(ClassMetadata::class)->reveal());
    }

    /**
     * @test
     */
    public function loader_should_call_correct_loader_class()
    {
        FileLoaderTestFileLoader::$called = false;
        $loader = new FilesLoader(['test1.yml'], FileLoaderTestFileLoader::class);
        $loader->loadClassMetadata($this->prophesize(ClassMetadata::class)->reveal());

        self::assertTrue(FileLoaderTestFileLoader::$called);
    }
}
