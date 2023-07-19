<?php

declare(strict_types=1);

namespace Kcs\Metadata\Loader;

use Kcs\Metadata\Exception\RuntimeException;

class FilesLoader extends ChainLoader
{
    /**
     * {@inheritdoc}
     *
     * @phpstan-param class-string<LoaderInterface>|null $loaderClass
     */
    public function __construct(array $paths, private string|null $loaderClass = null)
    {
        $loaders = [];
        foreach ($paths as $path) {
            $loaders[] = $this->getLoader($path);
        }

        parent::__construct($loaders);
    }

    /**
     * Create an instance of LoaderInterface for the path.
     */
    protected function getLoader(string $path): LoaderInterface
    {
        if ($this->loaderClass === null) {
            throw new RuntimeException('You must implement ' . __METHOD__ . ' or pass the loader class to the constructor');
        }

        return new $this->loaderClass($path);
    }
}
