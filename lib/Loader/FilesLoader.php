<?php declare(strict_types=1);

namespace Kcs\Metadata\Loader;

use Kcs\Metadata\Exception\RuntimeException;

class FilesLoader extends ChainLoader
{
    /**
     * @var string
     */
    private $loaderClass;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $paths, $loaderClass = null)
    {
        $this->loaderClass = $loaderClass;

        $loaders = [];
        foreach ($paths as $path) {
            $loaders[] = $this->getLoader($path);
        }

        parent::__construct($loaders);
    }

    /**
     * Create an instance of LoaderInterface for the path.
     *
     * @param $path
     *
     * @return LoaderInterface
     */
    protected function getLoader($path): LoaderInterface
    {
        if (null === $this->loaderClass) {
            throw new RuntimeException('You must implement '.__METHOD__.' or pass the loader class to the constructor');
        }

        return new $this->loaderClass($path);
    }
}
