<?php

declare(strict_types=1);

namespace PgTests\Utils\Parser;

use Pg\Utils\Parser\PhpTokenParser;
use PHPUnit\Framework\TestCase;

class PhpTokenParserTest extends TestCase
{
    public function testFindClassWithSimpleClass(): void
    {
        $code = <<<'PHP'
<?php

class TestClass {}
PHP;

        $result = PhpTokenParser::findClass($code);
        $this->assertEquals('\\TestClass', $result);
    }

    public function testFindClassWithSimpleClassNotStatic(): void
    {
        $code = <<<'PHP'
<?php

class TestClass {}
PHP;

        $parser = new PhpTokenParser();

        $result = $parser->findClass($code);
        $this->assertEquals('\\TestClass', $result);
    }

    public function testFindClassWithNamespace(): void
    {
        $code = <<<'PHP'
<?php

namespace TestNamespace;

class TestClass {}
PHP;

        $result = PhpTokenParser::findClass($code);
        $this->assertEquals('TestNamespace\TestClass', $result);
    }

    public function testFindClassWithNamespaceAndDoubleColon(): void
    {
        $code = <<<'PHP'
<?php

namespace TestNamespace;

use Pg\Attributes\Parser\PhpTokenParser;

$parseFunc = PhpTokenParser::findClass; 

class TestClass {}
PHP;

        $result = PhpTokenParser::findClass($code);
        $this->assertEquals('TestNamespace\TestClass', $result);
    }

    public function testFindClassWithMultipleNamespaces(): void
    {
        $code = <<<'PHP'
<?php

namespace Test\Namespace\SubNamespace;

class TestClass {}
PHP;

        $result = PhpTokenParser::findClass($code);
        $this->assertEquals('Test\Namespace\SubNamespace\TestClass', $result);
    }

    public function testFindClassWithNoClass(): void
    {
        $code = <<<'PHP'
<?php

function testFunction() {}
PHP;

        $result = PhpTokenParser::findClass($code);
        $this->assertFalse($result);
    }
}
