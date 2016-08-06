<?php

namespace Kcs\Metadata\Loader;

use Kcs\Metadata\ClassMetadataInterface;
use Kcs\Metadata\Exception\IOException;

abstract class FileLoader implements LoaderInterface
{
    use FileLoaderTrait;

    /**
     * @var string
     */
    private $filePath;

    /**
     * FileLoader constructor
     *
     * @param string $filePath
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * {@inheritdoc}
     */
    public function loadClassMetadata(ClassMetadataInterface $classMetadata)
    {
        $file_content = $this->loadFile($this->filePath);

        return $this->loadClassMetadataFromFile($file_content, $classMetadata);
    }

    /**
     * Load class metadata from file content
     *
     * @param string $file_content
     * @param ClassMetadataInterface $classMetadata
     *
     * @return bool
     */
    abstract protected function loadClassMetadataFromFile($file_content, ClassMetadataInterface $classMetadata);
}
