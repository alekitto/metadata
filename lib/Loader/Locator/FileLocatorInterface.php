<?php

namespace Kcs\Metadata\Loader\Locator;

interface FileLocatorInterface
{
    /**
     * Find all files matching $pattern glob
     *
     * @param string $basePath
     * @param string $pattern
     *
     * @return string[]
     */
    public function locate($basePath, $pattern);
}
