<?php declare(strict_types=1);

namespace Kcs\Metadata\Loader\Locator;

class IteratorFileLocator implements FileLocatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function locate($basePath, $extension): array
    {
        if ('.' !== $extension[0]) {
            throw new \InvalidArgumentException('Extension argument must start with a dot');
        }

        // Cannot use RecursiveDirectoryIterator::CURRENT_AS_PATHNAME because of this:
        // https://bugs.php.net/bug.php?id=66405

        $regex = '/'.preg_quote($extension, '/').'$/';
        $iterator = new \CallbackFilterIterator(
            new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($basePath, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY
            ),
            function (\SplFileInfo $fileInfo) use ($regex) {
                return preg_match($regex, $fileInfo->getPathname());
            }
        );

        return array_map(function (\SplFileInfo $fileInfo) {
            return $fileInfo->getPathname();
        }, iterator_to_array($iterator));
    }
}
