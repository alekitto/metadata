<?php

declare(strict_types=1);

namespace Kcs\Metadata\Loader\Locator;

interface FileLocatorInterface
{
    /**
     * Find all files matching $extension extension
     * NOTE: extension MUST start with a dot (.).
     *
     * @return string[]
     */
    public function locate(string $basePath, string $extension): array;
}
