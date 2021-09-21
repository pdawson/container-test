<?php

declare(strict_types=1);

namespace PaulDawson\ContainerTest\Dummy;

class CustomFoo extends Foo {
    public function __construct(string $property = 'test')
    {
        parent::__construct($property);
    }
}