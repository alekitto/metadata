<?php

namespace Kcs\Metadata\Tests\Loader\Locator;

use Kcs\Metadata\Loader\Locator\IteratorFileLocator;

class IteratorFileLocatorTest extends BaseFileLocatorTest
{
    /**
     * {@inheritdoc}
     */
    protected function getLocator()
    {
        return new IteratorFileLocator();
    }
}
