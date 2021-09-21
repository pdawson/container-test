# Crude Container

Just a very simple container implementation to break it down into small bite-sized pieces for explanation.

* Extends the PSR Container Interface
* Super simple auto-wiring included!

## Singletons

Singleton classes can be registered using the `singleton` method; the instances
will be stored against the container and returned over creating a new instance.

```php
$container->singleton(MyOtherClass::class, static function () {
    return new MyOtherClass();
});
```

## Registering

Register abstract classes, using `register` with a closure definition.

```php
$container->register(MyClass::class, static function (Container $container) {
    return new MyClass($container->get(MyOtherClass::class));
});

// You can also use ArrayAccess!
$container[MyClass::class] = fn () => new Definition();
```

## Creating

New instances can be created with `make`.

```php
$container->make(Abstract::class, [$parameters]);
```

## Retrieving

Retrieval of existing instances can be done with `get` or using `ArrayAccess`.

Where the abstract is not registered against the container, the container will
attempt to resolve it, along with any constructor arguments. 

If the abstract is a singleton and there is an instance, it's instance is always returned.
Otherwise, a new instance will be returned using the definition.

```php
$container->get(MyClass::class);

$container[MyOtherClass::class];
```