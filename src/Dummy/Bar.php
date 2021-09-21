<?php

declare(strict_types=1);

namespace PaulDawson\ContainerTest\Dummy;

class Bar
{
    public function helloWorld(): string
    {
        return 'hello, world!';
    }
}