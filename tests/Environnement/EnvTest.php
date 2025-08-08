<?php

namespace Entropy\Tests\Utils\Environnement;

use PHPUnit\Framework\TestCase;
use Entropy\Utils\Environnement\Env;

class EnvTest extends TestCase
{
    protected function setUp(): void
    {
        // Nettoyer les variables d'environnement avant chaque test
        putenv('TEST_VAR');
        putenv('TEST_BASE64');
    }

    public function testGetEnvReturnsValue(): void
    {
        putenv('TEST_VAR=hello');
        $this->assertEquals('hello', Env::getEnv('TEST_VAR'));
    }

    public function testGetEnvReturnsDefaultWhenNotSet(): void
    {
        $this->assertEquals('default', Env::getEnv('NONEXISTENT_VAR', 'default'));
    }

    public function testGetEnvReturnsNullWhenNotSetAndNoDefault(): void
    {
        $this->assertNull(Env::getEnv('NONEXISTENT_VAR'));
    }

    public function testGetEnvBase64Value(): void
    {
        $original = 'test secret';
        $encoded = base64_encode($original);
        putenv('TEST_BASE64=base64:' . $encoded);

        $this->assertEquals($encoded, Env::getEnv('TEST_BASE64'));
        $this->assertEquals($original, base64_decode(Env::getEnv('TEST_BASE64')));
    }

    protected function tearDown(): void
    {
        // Nettoyer les variables d'environnement après chaque test
        putenv('TEST_VAR');
        putenv('TEST_BASE64');
    }
}
