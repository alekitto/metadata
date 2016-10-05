<?php

namespace Kcs\Metadata\Tests\Loader\Processor;

use Kcs\Metadata\Loader\Processor\CompositeProcessor;

class CompositeProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function process_should_call_all_inner_processors()
    {
        $metadata = $this->prophesize('Kcs\Metadata\MetadataInterface')->reveal();
        $subject = new \stdClass();

        $processor1 = $this->prophesize('Kcs\Metadata\Loader\Processor\ProcessorInterface');
        $processor1->process($metadata, $subject)->shouldBeCalledTimes(1);

        $processor2 = $this->prophesize('Kcs\Metadata\Loader\Processor\ProcessorInterface');
        $processor2->process($metadata, $subject)->shouldBeCalledTimes(1);

        $processor = new CompositeProcessor([
            $processor1->reveal(),
            $processor2->reveal(),
        ]);
        $processor->process($metadata, $subject);
    }
}
