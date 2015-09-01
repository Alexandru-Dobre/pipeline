<?php

namespace League\Pipeline;

use InvalidArgumentException;

class Pipeline implements PipelineInterface
{
    /**
     * @var StageInterface[]
     */
    private $stages = [];

    /**
     * Constructor.
     *
     * @param callable[] $stages
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $stages = [])
    {
        foreach ($stages as $stage) {
            if (false === is_callable($stage)) {
                throw new InvalidArgumentException('All stages should be callable.');
            }
        }

        $this->stages = $stages;
    }

    /**
     * @inheritdoc
     */
    public function pipe(callable $stage)
    {
        $stages = $this->stages;
        $stages[] = $stage;

        return new static($stages);
    }

    /**
     * Process the payload.
     *
     * @param $payload
     *
     * @return mixed
     */
    public function process($payload)
    {
        $reducer = function ($payload, callable $stage) {
            return $stage($payload);
        };

        return array_reduce($this->stages, $reducer, $payload);
    }

    /**
     * @inheritdoc
     */
    public function __invoke($payload)
    {
        return $this->process($payload);
    }
}