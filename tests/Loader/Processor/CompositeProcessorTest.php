<?php declare(strict_types=1);

namespace Kcs\Metadata\Tests\Loader\Processor;

use Kcs\Metadata\Loader\Processor\CompositeProcessor;
use Kcs\Metadata\Loader\Processor\ProcessorInterface;
use Kcs\Metadata\MetadataInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class CompositeProcessorTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function process_should_call_all_inner_processors(): void
    {
        $metadata = $this->prophesize(MetadataInterface::class)->reveal();
        $subject = new \stdClass();

        $processor1 = $this->prophesize(ProcessorInterface::class);
        $processor1->process($metadata, $subject)->shouldBeCalledTimes(1);

        $processor2 = $this->prophesize(ProcessorInterface::class);
        $processor2->process($metadata, $subject)->shouldBeCalledTimes(1);

        $processor = new CompositeProcessor([
            $processor1->reveal(),
            $processor2->reveal(),
        ]);
        $processor->process($metadata, $subject);
    }
}
