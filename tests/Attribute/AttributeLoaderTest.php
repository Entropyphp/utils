<?php

declare(strict_types=1);

namespace PgTests\Utils\Attribute;

use Koriym\Attributes\AttributeReader;
use Pg\Utils\Attribute\AttributeLoader;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;

use function PgTests\Utils\Attribute\testFunctionWithAttribute;
use function PgTests\Utils\Attribute\testFunctionWithoutAttribute;

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

    public function testGetFunctionAttributeWithExistingAttribute(): void
    {
        $reflection = new ReflectionFunction('\PgTests\Utils\Attribute\testFunctionWithAttribute');
        $attribute = $this->attributeReader->getFunctionAttribute(
            $reflection,
            TestAttribute::class
        );

        $this->assertNotNull($attribute);
        $this->assertSame('test value', $attribute->value);
    }

    public function testGetFunctionAttributeWithNonExistingAttribute(): void
    {
        $reflection = new ReflectionFunction('\PgTests\Utils\Attribute\testFunctionWithoutAttribute');
        $attribute = $this->attributeReader->getFunctionAttribute(
            $reflection,
            TestAttribute::class
        );

        $this->assertNull($attribute);
    }

    public function testGetFunctionAttributesWithExistingAttributes(): void
    {
        $reflection = new ReflectionFunction('\PgTests\Utils\Attribute\testFunctionWithAttribute');
        $attributes = iterator_to_array($this->attributeReader->getFunctionAttributes(
            $reflection,
            TestAttribute::class
        ));

        $this->assertCount(1, $attributes);
        $this->assertSame('test value', $attributes[0]->value);
    }

    public function testGetFunctionAttributesWithNonExistingAttributes(): void
    {
        $reflection = new ReflectionFunction('\PgTests\Utils\Attribute\testFunctionWithoutAttribute');
        $generator = $this->attributeReader->getFunctionAttributes(
            $reflection,
            TestAttribute::class
        );

        $this->assertInstanceOf(\Generator::class, $generator);

        $this->assertNull($generator->current());
    }

    protected function setUp(): void
    {
        $this->attributeReader = new AttributeLoader(new AttributeReader());
    }
}
