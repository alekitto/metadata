<?php

declare(strict_types=1);

namespace Kcs\Metadata\Loader\Locator;

use InvalidArgumentException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use function array_map;
use function iterator_to_array;

class FinderFileLocator implements FileLocatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function locate(string $basePath, string $extension): array
    {
        if ($extension[0] !== '.') {
            throw new InvalidArgumentException('Extension argument must start with a dot');
        }

        $finder = Finder::create()
            ->files()
            ->in($basePath)
            ->name('*' . $extension);

        return array_map(static function (SplFileInfo $info) {
            return $info->getPathname();
        }, iterator_to_array($finder));
    }
}
