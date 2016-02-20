<?php

namespace Kcs\Metadata\Loader;

use Kcs\Metadata\ClassMetadataInterface;
use Kcs\Metadata\Exception\IOException;

abstract class FileLoader implements LoaderInterface
{
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
        $file_content = @file_get_contents($this->filePath);
        if (false === $file_content) {
            $error = error_get_last();

            throw new IOException(sprintf(
                "Cannot load file '%s': %s",
                $this->filePath,
                isset($error['message']) ? $error['message'] : 'Unknown error'
            ));
        }

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
