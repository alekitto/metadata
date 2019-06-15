<?php declare(strict_types=1);

namespace Kcs\Metadata\Factory;

use Kcs\Metadata\ClassMetadata;
use Kcs\Metadata\ClassMetadataInterface;
use Kcs\Metadata\Exception\InvalidArgumentException;

class MetadataFactory extends AbstractMetadataFactory
{
    /**
     * Metadata object to be created.
     *
     * @var string
     */
    private $metadataClass = ClassMetadata::class;

    /**
     * Set the metadata class to be created by this factory.
     *
     * @param string $metadataClass
     */
    public function setMetadataClass(string $metadataClass): void
    {
        if (! \class_exists($metadataClass) || ! (new \ReflectionClass($metadataClass))->implementsInterface(ClassMetadataInterface::class)) {
            throw InvalidArgumentException::create(InvalidArgumentException::INVALID_METADATA_CLASS, $metadataClass);
        }

        $this->metadataClass = $metadataClass;
    }

    /**
     * {@inheritdoc}
     */
    protected function createMetadata(\ReflectionClass $class): ClassMetadataInterface
    {
        return new $this->metadataClass($class);
    }
}
