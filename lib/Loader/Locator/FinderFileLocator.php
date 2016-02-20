<?php

namespace Kcs\Metadata\Loader\Locator;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class FinderFileLocator implements FileLocatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function locate($basePath, $pattern)
    {
        $finder = Finder::create()
            ->files()
            ->in($basePath)
            ->name($pattern);

        return array_map(function (SplFileInfo $info) {
            return $info->getPathname();
        }, iterator_to_array($finder));
    }
}
