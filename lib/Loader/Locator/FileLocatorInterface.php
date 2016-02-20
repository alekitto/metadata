<?php

namespace Kcs\Metadata\Loader\Locator;

interface FileLocatorInterface
{
    /**
     * Find all files matching $extension extension
     * NOTE: extension MUST start with a dot (.)
     *
     * @param string $basePath
     * @param string $extension
     *
     * @return string[]
     */
    public function locate($basePath, $extension);
}
