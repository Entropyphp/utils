<?php

declare(strict_types=1);

namespace Entropy\Tests\Utils\Attribute;

use Doctrine\Common\Annotations\Reader;
use Koriym\Attributes\AttributeReader;
use Entropy\Utils\Attribute\AttributeLoader;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;

use function Entropy\Tests\Utils\Attribute\testFunctionWithAttribute;
use function Entropy\Tests\Utils\Attribute\testFunctionWithoutAttribute;

class AttributeLoaderTest extends TestCase
{
    private AttributeLoader $attributeReader;

    public function testConstructor(): void
    {
        $attributeLoader = new AttributeLoader();
        $this->assertInstanceOf(Reader::class, $attributeLoader->getReader());
        $this->assertInstanceOf(AttributeReader::class, $attributeLoader->getReader());
    }

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
        $reflection = new ReflectionFunction('\Entropy\Tests\Utils\Attribute\testFunctionWithAttribute');
        $attribute = $this->attributeReader->getFunctionAttribute(
            $reflection,
            TestAttribute::class
        );

        $this->assertNotNull($attribute);
        $this->assertSame('test value', $attribute->value);
    }

    public function testGetFunctionAttributeWithNonExistingAttribute(): void
    {
        $reflection = new ReflectionFunction('\Entropy\Tests\Utils\Attribute\testFunctionWithoutAttribute');
        $attribute = $this->attributeReader->getFunctionAttribute(
            $reflection,
            TestAttribute::class
        );

        $this->assertNull($attribute);
    }

    public function testGetFunctionAttributesWithExistingAttributes(): void
    {
        $reflection = new ReflectionFunction('\Entropy\Tests\Utils\Attribute\testFunctionWithAttribute');
        $attributes = $this->attributeReader->getFunctionAttributes(
            $reflection,
            TestAttribute::class
        );


        $this->assertCount(1, $attributes);
        $this->assertSame('test value', $attributes[0]->value);
    }

    public function testGetFunctionAttributesWithNonExistingAttributes(): void
    {
        $reflection = new ReflectionFunction('\Entropy\Tests\Utils\Attribute\testFunctionWithoutAttribute');
        $attributes = $this->attributeReader->getFunctionAttributes(
            $reflection,
            TestAttribute::class
        );


        $this->assertEmpty($attributes);
    }

    protected function setUp(): void
    {
        $this->attributeReader = new AttributeLoader(new AttributeReader());
    }
}
