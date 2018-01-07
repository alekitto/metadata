<?php declare(strict_types=1);

namespace Kcs\Metadata;

trait AttributeMetadataTrait
{
    /**
     * @var string
     */
    public $class;

    /**
     * @var string
     */
    public $name;

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }
}
