<?php declare(strict_types=1);

namespace Kcs\Metadata\Tests\Loader;

use Kcs\Metadata\ClassMetadata;
use Kcs\Metadata\Loader\ChainLoader;
use Kcs\Metadata\Loader\LoaderInterface;
use PHPUnit\Framework\TestCase;

class ChainLoaderTest extends TestCase
{
    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function constructor_should_throw_on_non_loader_instance()
    {
        $loaders = [
            new \stdClass(),
        ];

        new ChainLoader($loaders);
    }

    /**
     * @test
     */
    public function load_metadata_should_call_all_loaders()
    {
        $loader1 = $this->prophesize(LoaderInterface::class);
        $loader2 = $this->prophesize(LoaderInterface::class);
        $loader3 = $this->prophesize(LoaderInterface::class);

        $metadata = new ClassMetadata(new \ReflectionClass($this));

        $loader1->loadClassMetadata($metadata)->shouldBeCalled();
        $loader2->loadClassMetadata($metadata)->shouldBeCalled();
        $loader3->loadClassMetadata($metadata)->shouldBeCalled();

        $loader = new ChainLoader([
            $loader1->reveal(),
            $loader2->reveal(),
            $loader3->reveal(),
        ]);
        $loader->loadClassMetadata($metadata);
    }
}
