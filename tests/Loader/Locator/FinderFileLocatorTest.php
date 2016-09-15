<?php

namespace Kcs\Metadata\Tests\Loader\Locator;

use Kcs\Metadata\Loader\Locator\FinderFileLocator;

class FinderFileLocatorTest extends BaseFileLocatorTest
{
    /**
     * {@inheritdoc}
     */
    protected function getLocator()
    {
        return new FinderFileLocator();
    }
}
