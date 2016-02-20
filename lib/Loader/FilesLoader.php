<?php

namespace Kcs\Metadata\Loader;

abstract class FilesLoader extends ChainLoader
{
    /**
     * {@inheritdoc}
     */
    public function __construct(array $paths)
    {
        $loaders = [];
        foreach ($paths as $path) {
            $loaders[] = $this->getLoader($path);
        }

        parent::__construct($loaders);
    }

    /**
     * Create an instance of LoaderInterface for the path
     *
     * @param $path
     * @return LoaderInterface
     */
    abstract protected function getLoader($path);
}
