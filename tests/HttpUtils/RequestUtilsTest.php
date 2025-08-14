<?php

namespace Entropy\Tests\Utils\HttpUtils;

use Entropy\Utils\HttpUtils\RequestUtils;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class RequestUtilsTest extends TestCase
{
    public function testIsAjaxTrue()
    {
        $request = new ServerRequest('GET', '/test', ['X-Requested-With' => 'XMLHttpRequest']);

        $this->assertTrue(RequestUtils::isAjax($request));
    }

    public function testIsAjaxFalse()
    {
        $request = new ServerRequest('GET', '/test');

        $this->assertFalse(RequestUtils::isAjax($request));
    }

    public function testIsJsonTrue()
    {
        $request = new ServerRequest('GET', '/test', ['content-type' => 'application/json']);
        $this->assertTrue(RequestUtils::isJson($request));
    }

    public function testIsJsonFalse()
    {
        $request = new ServerRequest('GET', '/test', ['content-type' => 'application/text']);
        $this->assertFalse(RequestUtils::isJson($request));
    }

    public function testGetParsedBodyJson()
    {
        $expected = ['name' => 'test'];
        $request = new ServerRequest(
            'POST',
            '/test',
            ['content-type' => 'application/json'],
            '{"name": "test"}'
        );

        $actual = RequestUtils::getPostParams($request);

        $this->assertEquals($expected, $actual);
        $this->assertIsArray($actual);
    }

    public function testGetParsedBodyBadJson()
    {
        $request = new ServerRequest(
            'POST',
            '/test',
            ['content-type' => 'application/json'],
            '{"name": "test"'
        );

        $actual = RequestUtils::getPostParams($request);

        $this->assertEquals([], $actual);
        $this->assertIsArray($actual);
    }
    public function testGetParsedBodyNotJson()
    {
        $expected = ["name" => "test"];
        $request = (new ServerRequest(
            'POST',
            '/test',
            ['content-type' => 'multipart/form-data']
        ))
        ->withParsedBody($expected);

        $actual = RequestUtils::getPostParams($request);

        $this->assertEquals($expected, $actual);
        $this->assertIsArray($actual);
    }

    public function testGetAcceptFormatJson()
    {
        $request = new ServerRequest('GET', '/test', ['Accept' => 'application/json']);
        $this->assertEquals('json', RequestUtils::getAcceptFormat($request));
    }

    public function testGetAcceptFormatHtml()
    {
        $request = new ServerRequest('GET', '/test', ['Accept' => 'application/text']);
        $this->assertEquals('html', RequestUtils::getAcceptFormat($request));
    }

    public function testWantJson()
    {
        $request = new ServerRequest('GET', '/test', ['Accept' => 'application/json']);
        $this->assertTrue(RequestUtils::wantJson($request));
    }

    public function testWantJsonNoJsonRequest()
    {
        $request = new ServerRequest('GET', '/test', ['Accept' => 'application/text']);
        $this->assertFalse(RequestUtils::wantJson($request));
    }

    public function testGetDomain()
    {
        $request = new ServerRequest('GET', 'https://www.example.com:80/test');
        $this->assertEquals('https://www.example.com:80', RequestUtils::getDomain($request));
    }
}
