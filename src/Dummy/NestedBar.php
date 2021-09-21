<?php

declare(strict_types=1);

namespace PaulDawson\ContainerTest\Dummy;

class NestedBar
{
    public function __construct(
        protected Bar $bar
    ) {}

    /**
     * @return Bar
     */
    public function getBar(): Bar
    {
        return $this->bar;
    }
}