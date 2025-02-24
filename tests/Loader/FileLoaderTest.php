<?php declare(strict_types=1);

namespace Kcs\Metadata\Tests\Loader;

use Kcs\Metadata\ClassMetadata;
use Kcs\Metadata\ClassMetadataInterface;
use Kcs\Metadata\Exception\IOException;
use Kcs\Metadata\Loader\FileLoader;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class TestLoader extends FileLoader
{
    protected function loadClassMetadataFromFile(string $fileContent, ClassMetadataInterface $classMetadata): bool
    {
        return true;
    }
}

class FileLoaderTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    #[Test]
    public function load_class_metadata_should_throw_if_file_cannot_be_read(): void
    {
        $baseDir = realpath(__DIR__ . '/../Fixtures/FileLoader');
        $loader = new TestLoader($baseDir.'/does_not_exists.yml');

        $this->expectException(IOException::class);
        $loader->loadClassMetadata($this->prophesize(ClassMetadata::class)->reveal());
    }

    /**
     * @test
     */
    #[Test]
    public function load_class_metadata_loads(): void
    {
        $baseDir = realpath(__DIR__ . '/../Fixtures/FileLoader');
        $loader = new TestLoader($baseDir);
        self::assertTrue($loader->loadClassMetadata($this->prophesize(ClassMetadata::class)->reveal()));
    }
}
