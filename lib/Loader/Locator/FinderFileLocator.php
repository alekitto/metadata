<?php

namespace Kcs\Metadata\Loader\Locator;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class FinderFileLocator implements FileLocatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function locate($basePath, $extension)
    {
        if ($extension[0] !== '.') {
            throw new \InvalidArgumentException('Extension argument must start with a dot');
        }

        $finder = Finder::create()
            ->files()
            ->in($basePath)
            ->name('*'.$extension);

        return array_map(function (SplFileInfo $info) {
            return $info->getPathname();
        }, iterator_to_array($finder));
    }
}