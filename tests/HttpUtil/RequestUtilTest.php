<?php

namespace Entropy\Tests\Utils\HttpUtil;

use Entropy\Utils\HttpUtil\RequestUtil;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\TestCase;

class RequestUtilTest extends TestCase
{
    public function testIsAjaxTrue()
    {
        $request = new ServerRequest('GET', '/test', ['X-Requested-With' => 'XMLHttpRequest']);

        $this->assertTrue(RequestUtil::isAjax($request));
    }

    public function testIsJsonTrue()
    {
        $request = new ServerRequest('GET', '/test', ['content-type' => 'application/json']);
        $this->assertTrue(RequestUtil::isJson($request));
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

        $actual = RequestUtil::getPostParams($request);

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

        $actual = RequestUtil::getPostParams($request);

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

        $actual = RequestUtil::getPostParams($request);

        $this->assertEquals($expected, $actual);
        $this->assertIsArray($actual);
    }

    public function testGetAcceptFormatJson()
    {
        $request = new ServerRequest('GET', '/test', ['Accept' => 'application/json']);
        $this->assertEquals('json', RequestUtil::getAcceptFormat($request));
    }

    public function testGetAcceptFormatHtml()
    {
        $request = new ServerRequest('GET', '/test', ['Accept' => 'application/text']);
        $this->assertEquals('html', RequestUtil::getAcceptFormat($request));
    }

    public function testWantJson()
    {
        $request = new ServerRequest('GET', '/test', ['Accept' => 'application/json']);
        $this->assertTrue(RequestUtil::wantJson($request));
    }

    public function testWantJsonNoJsonRequest()
    {
        $request = new ServerRequest('GET', '/test', ['Accept' => 'application/text']);
        $this->assertFalse(RequestUtil::wantJson($request));
    }

    public function testGetDomain()
    {
        $request = new ServerRequest('GET', 'https://www.example.com:80/test');
        $this->assertEquals('https://www.example.com:80', RequestUtil::getDomain($request));
    }
}
