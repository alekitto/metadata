<?php declare(strict_types=1);

namespace Kcs\Metadata\Tests\Loader\Locator;

use Kcs\Metadata\Loader\Locator\FileLocatorInterface;
use Kcs\Metadata\Loader\Locator\IteratorFileLocator;

class IteratorFileLocatorTest extends BaseFileLocatorTests
{
    /**
     * {@inheritdoc}
     */
    protected function getLocator(): FileLocatorInterface
    {
        return new IteratorFileLocator();
    }
}
