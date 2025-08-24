<?php

declare(strict_types=1);

namespace App\Core\Serializer\Denormalizer;

use DateTimeImmutable;
use Exception;
use ReflectionClass;

class Denormalizer
{
    /**
     * Denormalisiert ein Array zu einem Objekt der angegebenen Klasse.
     *
     * @template T
     * @param array $data
     * @param class-string<T> $className
     * @return T
     * @throws Exception
     */
    public function denormalize(array $data, string $className): object
    {
        if (!class_exists($className)) {
            throw new Exception("Klasse $className existiert nicht.");
        }

        $reflectionClass = new ReflectionClass($className);
        $object = $reflectionClass->newInstanceWithoutConstructor();

        foreach ($data as $key => $value) {
            $camelCase = $this->snakeToCamel($key);
            if (!$reflectionClass->hasProperty($camelCase)) {
                // Falls das Array Keys enthält, die das Objekt nicht hat, überspringen wir sie
                continue;
            }

            $property = $reflectionClass->getProperty($camelCase);
            $property->setAccessible(true);

            // Optional: Typ prüfen (PHP 7.4+)
            $type = $property->getType();
            if ($type) {
                $typeName = $type->getName();

                if (($typeName === 'DateTime' || $typeName === 'DateTimeImmutable') && !is_null($value)) {
                    $value = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $value);
                }

                if (class_exists($typeName) && is_array($value)) {
                    // Falls das Property ein Objekt ist -> rekursiv denormalisieren
                    $value = $this->denormalize($value, $typeName);
                } elseif ($type->isBuiltin()) {
                    settype($value, $typeName); // Skalarer Typ wird erzwungen
                }
            }
            $property->setValue($object, $value);
        }
        return $object;
    }

    private function snakeToCamel(string $string): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $string))));
    }
}