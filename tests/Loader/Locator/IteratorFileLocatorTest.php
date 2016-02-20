<?php

namespace Kcs\Metadata\Tests\Loader\Locator;

use Kcs\Metadata\Loader\Locator\IteratorFileLocator;

class IteratorFileLocatorTest extends BaseFileLocatorTest
{
    /**
     * @inheritDoc
     */
    protected function getLocator()
    {
        return new IteratorFileLocator();
    }
}
