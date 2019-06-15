<?php declare(strict_types=1);

namespace Kcs\Metadata\Loader\Processor;

use Kcs\Metadata\Exception\InvalidArgumentException;

class ProcessorFactory implements ProcessorFactoryInterface
{
    /**
     * @var string[]
     */
    private $processors = [];

    /**
     * @var ProcessorInterface[]
     */
    private $instances = [];

    /**
     * Register a processor class for $class.
     *
     * @param string $class
     * @param string $processorClass
     */
    public function registerProcessor(string $class, string $processorClass): void
    {
        if (! (new \ReflectionClass($processorClass))->implementsInterface(ProcessorInterface::class)) {
            throw InvalidArgumentException::create(InvalidArgumentException::INVALID_PROCESSOR_INTERFACE_CLASS, $processorClass);
        }

        if (! isset($this->processors[$class])) {
            $this->processors[$class] = $processorClass;
        } elseif (! \is_array($this->processors[$class])) {
            $this->processors[$class] = [$this->processors[$class], $processorClass];
        } else {
            $this->processors[$class][] = $processorClass;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getProcessor($class): ?ProcessorInterface
    {
        if (\is_object($class)) {
            $class = \get_class($class);
        }

        if (! isset($this->processors[$class])) {
            return null;
        }

        if (isset($this->instances[$class])) {
            return $this->instances[$class];
        }

        $processor = $this->processors[$class];
        if (\is_array($processor)) {
            return $this->instances[$class] = $this->createComposite($processor);
        }

        return $this->instances[$class] = new $processor();
    }

    /**
     * Create a CompositeProcessor instance.
     *
     * @param ProcessorInterface[] $processors
     *
     * @return CompositeProcessor
     */
    private function createComposite(array $processors): CompositeProcessor
    {
        return new CompositeProcessor(\array_map(static function ($class) {
            return new $class();
        }, $processors));
    }
}
