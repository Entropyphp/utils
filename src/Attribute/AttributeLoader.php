<?php

declare(strict_types=1);

namespace Entropy\Utils\Attribute;

use koriym\Attributes\AttributeReader;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;

class AttributeLoader
{
    public function __construct(private ?AttributeReader $reader = null)
    {
        if ($this->reader === null) {
            $this->reader = new AttributeReader();
        }
    }

    /**
     * @param ReflectionMethod $method
     * @param string $attributeClassName
     * @return object|null
     */
    public function getMethodAttribute(ReflectionMethod $method, string $attributeClassName): ?object
    {
        return $this->reader->getMethodAnnotation($method, $attributeClassName);
    }

    /**
     * @param ReflectionMethod $method
     * @param string $attributeClassName
     * @return iterable|null
     */
    public function getMethodAttributes(ReflectionMethod $method, string $attributeClassName): ?iterable
    {
        $attributes = $this->reader->getMethodAnnotations($method);

        foreach ($attributes as $annotation) {
            if ($annotation instanceof $attributeClassName) {
                yield $annotation;
            }
        }
        return null;
    }

    /**
     * @param ReflectionClass $class
     * @param string $attributeClassName
     * @return object|null
     */
    public function getClassAttribute(ReflectionClass $class, string $attributeClassName): ?object
    {
        return $this->reader->getClassAnnotation($class, $attributeClassName);
    }

    /**
     * @param ReflectionClass $class
     * @param string $attributeClassName
     * @return iterable|null
     */
    public function getClassAttributes(ReflectionClass $class, string $attributeClassName): ?iterable
    {
        $attributes = $this->reader->getClassAnnotations($class);

        foreach ($attributes as $annotation) {
            if ($annotation instanceof $attributeClassName) {
                yield $annotation;
            }
        }
        return null;
    }

    /**
     * @param ReflectionFunction $function
     * @param string $attributeClassName
     * @return object|null
     *
     * @template T of object
     */
    public function getFunctionAttribute(ReflectionFunction $function, string $attributeClassName): ?object
    {
        $attributes = $function->getAttributes($attributeClassName, ReflectionAttribute::IS_INSTANCEOF);
        if (isset($attributes[0])) {
            /** @var T $object */
            $object = $attributes[0]->newInstance();

            return $object;
        }

        return null;
    }

    /**
     * Gets the attributes applied to a function.
     *
     * @param ReflectionFunction $function The ReflectionFunction of the Function from which
     * the attributes should be read.
     * @param class-string<T>|null $attributeClassName – Name of an attribute class, default to null.
     *
     * @return array<int, object<T>> An array of Attributes class T.
     * @template T of object
     */
    public function getFunctionAttributes(ReflectionFunction $function, string $attributeClassName = null): array
    {
        $attributesRefs = $function->getAttributes($attributeClassName, ReflectionAttribute::IS_INSTANCEOF);

        $attributes = [];
        foreach ($attributesRefs as $ref) {
            $attributes[] = $ref->newInstance();
        }

        return $attributes;
    }

    public function getReader(): AttributeReader
    {
        return $this->reader;
    }
}
