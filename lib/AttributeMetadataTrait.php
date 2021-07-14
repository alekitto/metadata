<?php

declare(strict_types=1);

namespace Kcs\Metadata;

trait AttributeMetadataTrait
{
    /** @phpstan-var class-string */
    public string $class;
    public string $name;

    public function getName(): string
    {
        return $this->name;
    }
}
