<?php

namespace MethodBus\Core;

use ReflectionClass;
use ReflectionNamedType;
use RuntimeException;

final class Container
{
    private array $bindings = [];
    private array $singletons = [];
    private array $instances = [];

    public function bind(string $abstract, string $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    public function singleton(string $abstract, string $concrete): void
    {
        $this->singletons[$abstract] = $concrete;
    }

    public function make(string $abstract): object
    {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        $concrete = $this->bindings[$abstract]
            ?? $this->singletons[$abstract]
            ?? $abstract;

        $object = $this->resolve($concrete);

        if (isset($this->singletons[$abstract])) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    private function resolve(string $class): object
    {
        if (!class_exists($class)) {
            throw new RuntimeException("Class [$class] not found");
        }

        $reflection = new ReflectionClass($class);

        if (!$reflection->isInstantiable()) {
            throw new RuntimeException("Class [$class] is not instantiable");
        }

        $constructor = $reflection->getConstructor();

        if (!$constructor) {
            return new $class;
        }

        $dependencies = [];

        foreach ($constructor->getParameters() as $parameter) {

            $type = $parameter->getType();

            if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                throw new RuntimeException(
                    "Unable to resolve parameter [{$parameter->getName()}] in class [$class]"
                );
            }

            $dependencies[] = $this->make($type->getName());
        }

        return $reflection->newInstanceArgs($dependencies);
    }
}