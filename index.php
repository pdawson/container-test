<?php

declare(strict_types=1);

use PaulDawson\ContainerTest\Container;
use PaulDawson\ContainerTest\Dummy\Baz;
use PaulDawson\ContainerTest\Dummy\CustomFoo;
use PaulDawson\ContainerTest\Dummy\Foo;
use PaulDawson\ContainerTest\Dummy\Bar;
use PaulDawson\ContainerTest\Dummy\NestedBar;
use PaulDawson\ContainerTest\Dummy\NestedBaz;

require_once 'vendor/autoload.php';

//
// Create a new instance of the container
//
$container = Container::instance();

//
// Register container bindings
//
$container[Foo::class] = static fn () => new Foo('test');

$container->singleton(
    Bar::class,
    static fn () => new Bar()
);

$container->register(
    NestedBar::class,
    static fn (Container $container)
        => new NestedBar($container->get(Bar::class))
);

$container->register(
    CustomFoo::class,
    static fn (Container $container, array $arguments)
        => new CustomFoo(...$arguments)
);

// Note: We do not nest Baz here, this can be auto-wired from the container

//
// Testing the container
// Could use PHP unit, but this is simpler for explanation.
//

/** @var Foo $foo */
$foo = $container[Foo::class];

assert($foo instanceof Foo);
assert($foo->getProperty() === 'test');

/** @var Bar $bar */
$bar = $container->get(Bar::class);

assert($bar instanceof Bar);
assert($bar->helloWorld() === 'hello, world!');
assert($bar === $container->get(Bar::class));
assert($bar === $container[Bar::class]);

/** @var Baz $baz */
$baz = $container->get(Baz::class);

assert($baz instanceof Baz);
assert($baz->helloWorld() === 'no, goodbye!');

/** @var NestedBar $nestedBar */
$nestedBar = $container->get(NestedBar::class);

assert($nestedBar instanceof NestedBar);
assert($nestedBar->getBar() instanceof Bar);

// This one is registered as a singleton; we would expect the instances to be
// exactly the same, so we can compare them strictly.
assert($nestedBar->getBar() === $container->get(Bar::class));
assert($nestedBar->getBar() === $container[Bar::class]);

//
// We can also auto-wire the nested classes!
//
$nestedBaz = $container->get(NestedBaz::class);

assert($nestedBaz instanceof NestedBaz);
assert($nestedBaz->getBaz() instanceof Baz);
assert($nestedBaz->getBaz() !== $baz);

$customFoo = $container->make(CustomFoo::class, ['A custom parameter']);

assert($customFoo instanceof CustomFoo);
assert($customFoo->getProperty() === 'A custom parameter');

$anotherCustomFoo = $container->make(CustomFoo::class, ['Another instance']);

assert($anotherCustomFoo instanceof CustomFoo);
assert($anotherCustomFoo !== $customFoo);
assert($anotherCustomFoo->getProperty() === 'Another instance');
