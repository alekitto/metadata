<?php

namespace Kcs\Metadata\Tests\Loader\Locator;

use Kcs\Metadata\Loader\Locator\FileLocatorInterface;
use Kcs\Metadata\Loader\Locator\FinderFileLocator;

class FinderFileLocatorTest extends BaseFileLocatorTest
{
    /**
     * @inheritDoc
     */
    protected function getLocator()
    {
        return new FinderFileLocator();
    }
}
