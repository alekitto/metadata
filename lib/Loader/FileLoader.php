<?php

declare(strict_types=1);

namespace Kcs\Metadata\Loader;

use Kcs\Metadata\ClassMetadataInterface;

abstract class FileLoader implements LoaderInterface
{
    use FileLoaderTrait;

    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function loadClassMetadata(ClassMetadataInterface $classMetadata): bool
    {
        $content = $this->loadFile($this->filePath);

        return $this->loadClassMetadataFromFile($content, $classMetadata);
    }

    /**
     * Load class metadata from file content.
     */
    abstract protected function loadClassMetadataFromFile(string $fileContent, ClassMetadataInterface $classMetadata): bool;
}
