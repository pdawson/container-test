<?php

declare(strict_types=1);

namespace PaulDawson\ContainerTest\Dummy;

class NestedBaz
{
    public function __construct(
        protected Baz $baz
    ) {
    }

    /**
     * @return Baz
     */
    public function getBaz(): Baz
    {
        return $this->baz;
    }
}