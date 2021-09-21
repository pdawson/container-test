<?php

declare(strict_types=1);

namespace PaulDawson\ContainerTest\Dummy;

class Foo {
    public function __construct(
        protected string $property
    ) {}

    /**
     * @return string
     */
    public function getProperty(): string
    {
        return $this->property;
    }

    /**
     * @param string $property
     */
    public function setProperty(string $property): void
    {
        $this->property = $property;
    }
}