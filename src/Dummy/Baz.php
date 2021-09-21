<?php

declare(strict_types=1);

namespace PaulDawson\ContainerTest\Dummy;

class Baz extends Bar
{
    public function helloWorld(): string
    {
        return 'no, goodbye!';
    }
}