<?php

namespace Entropy\Tests\Utils\HttpUtils;

use Entropy\Utils\HttpUtils\JsonResponse;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class JsonResponseTest extends TestCase
{
    public function testJsonResponse()
    {
        $response = new JsonResponse(200, json_encode(['test' => 'test']));

        $this->assertInstanceOf(Response::class, $response);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('{"test":"test"}', (string) $response->getBody());
        $this->assertEquals('application/json;charset=UTF-8', $response->getHeaderLine('Content-Type'));
    }
}
