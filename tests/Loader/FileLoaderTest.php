<?php declare(strict_types=1);

namespace Kcs\Metadata\Tests\Loader;

use Kcs\Metadata\ClassMetadata;
use Kcs\Metadata\ClassMetadataInterface;
use Kcs\Metadata\Exception\IOException;
use Kcs\Metadata\Loader\FileLoader;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;

class TestLoader extends FileLoader
{
    protected function loadClassMetadataFromFile(string $file_content, ClassMetadataInterface $classMetadata): bool
    {
        return true;
    }
}

class FileLoaderTest extends TestCase
{
    /**
     * @test
     */
    public function load_class_metadata_should_throw_if_file_cannot_be_read()
    {
        $this->expectException(IOException::class);
        $root = vfsStream::setup();
        $loader = new TestLoader($root->url().'/does_not_exists.yml');
        $loader->loadClassMetadata($this->prophesize(ClassMetadata::class)->reveal());
    }

    /**
     * @test
     */
    public function load_class_metadata_loads()
    {
        $root = vfsStream::setup();
        $root->addChild($file = new vfsStreamFile('mapping.xml'));

        $loader = new TestLoader($file->url());
        self::assertTrue(
            $loader->loadClassMetadata($this->prophesize(ClassMetadata::class)->reveal())
        );
    }
}
