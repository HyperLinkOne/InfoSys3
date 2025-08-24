<?php

declare(strict_types=1);

namespace App\Core;

use Closure;
use Exception;
use ReflectionClass;

class Container implements ContainerInterface
{
    private array $bindings = [];

    public function set(string $id, Closure $factory): void
    {
        $this->bindings[$id] = $factory;
    }

    public function get(string $id): mixed
    {
        if ($this->has($id)) {
            return $this->bindings[$id]($this);
        }

        if (class_exists($id)) {
            return $this->autoWire($id);
        }

        throw new \RuntimeException("No binding found for $id");
    }

    public function has(string $id): bool
    {
        if (isset($this->bindings[$id])) {
            return true;
        }
        return false;
    }

    private function autoWire(string $class): object
    {
        $reflection = new ReflectionClass($class);
        $constructor = $reflection->getConstructor();

        if (!$constructor) {
            return new $class();
        }

        $params = [];
        foreach ($constructor->getParameters() as $param) {
            $paramType = $param->getType();

            if ($paramType && !$paramType->isBuiltin()) {
                $paramClass = $paramType->getName();
                $params[] = $this->get($paramClass);
            } elseif ($param->isDefaultValueAvailable()) {
                $params[] = $param->getDefaultValue();
            } else {
                throw new \RuntimeException("Cannot resolve parameter \${$param->getName()} for class $class");
            }
        }

        return $reflection->newInstanceArgs($params);
    }

}
