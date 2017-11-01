<?php declare(strict_types=1);

namespace Kcs\Metadata\Tests\Loader\Locator;

use Kcs\Metadata\Loader\Locator\FileLocatorInterface;
use Kcs\Metadata\Loader\Locator\FinderFileLocator;

class FinderFileLocatorTest extends BaseFileLocatorTest
{
    /**
     * {@inheritdoc}
     */
    protected function getLocator(): FileLocatorInterface
    {
        return new FinderFileLocator();
    }
}
