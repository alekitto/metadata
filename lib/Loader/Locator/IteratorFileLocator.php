<?php

namespace Kcs\Metadata\Loader\Locator;

class IteratorFileLocator implements FileLocatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function locate($basePath, $extension)
    {
        if ($extension[0] !== '.') {
            throw new \InvalidArgumentException('Extension argument must start with a dot');
        }

        $flags = \RecursiveDirectoryIterator::SKIP_DOTS | \RecursiveDirectoryIterator::CURRENT_AS_PATHNAME;
        $iterator = new \RegexIterator(
            new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($basePath, $flags),
                \RecursiveIteratorIterator::LEAVES_ONLY
            ),
            '/'.preg_quote($extension, '/').'$/'
        );

        return iterator_to_array($iterator);
    }
}
