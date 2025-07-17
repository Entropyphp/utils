<?php

declare(strict_types=1);

namespace PgTests\Utils\Attribute;

use Koriym\Attributes\AttributeReader;
use Pg\Utils\Attribute\AttributeLoader;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class AttributeLoaderTest extends TestCase
{
    private AttributeLoader $attributeReader;

    /**
     * @throws ReflectionException
     */
    public function testGetMethodAttribute(): void
    {
        $class = new class {
            #[TestAttribute]
            public function testMethod()
            {
            }
        };

        $method = new ReflectionMethod($class, 'testMethod');
        $attributeClassName = TestAttribute::class;

        $result = $this->attributeReader->getMethodAttribute($method, $attributeClassName);
        $this->assertNotNull($result);
    }

    /**
     * @throws ReflectionException
     */
    public function testGetMethodAttributes(): void
    {
        $class = new class {
            #[TestAttribute]
            #[TestAttribute]
            public function testMethod()
            {
            }
        };

        $method = new ReflectionMethod($class, 'testMethod');
        $attributeClassName = TestAttribute::class;

        $result = iterator_to_array($this->attributeReader->getMethodAttributes($method, $attributeClassName));
        $this->assertCount(2, $result);
    }

    public function testGetClassAttribute(): void
    {
        $class = new TestClass();
        $reflectionClass = new ReflectionClass($class);
        $attributeClassName = TestAttribute::class;

        $result = $this->attributeReader->getClassAttribute($reflectionClass, $attributeClassName);
        $this->assertNotNull($result);
    }

    public function testGetClassAttributeIsNull(): void
    {
        $class = new class {
            #[TestAttribute]
            public function testMethod()
            {
            }
        };
        $reflectionClass = new ReflectionClass($class);
        $attributeClassName = TestAttribute::class;

        $result = $this->attributeReader->getClassAttribute($reflectionClass, $attributeClassName);
        $this->assertNull($result);
    }

    public function testGetClassAttributes(): void
    {
        $class = new TestClass();
        $reflectionClass = new ReflectionClass($class);
        $attributeClassName = TestAttribute::class;

        $result = iterator_to_array($this->attributeReader->getClassAttributes($reflectionClass, $attributeClassName));
        $this->assertCount(2, $result);
    }

    public function testGetReader(): void
    {
        $reader = $this->attributeReader->getReader();
        $this->assertInstanceOf(AttributeReader::class, $reader);
    }

    protected function setUp(): void
    {
        $this->attributeReader = new AttributeLoader(new AttributeReader());
    }
}
