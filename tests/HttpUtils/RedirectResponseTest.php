<?php

namespace Entropy\Tests\Utils\HttpUtils;

use Entropy\Utils\HttpUtils\RedirectResponse;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class RedirectResponseTest extends TestCase
{
    public function testRedirectResponse()
    {
        $response = new RedirectResponse('/test');

        $this->assertInstanceOf(Response::class, $response);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals('/test', $response->getHeaderLine('Location'));
    }
}
