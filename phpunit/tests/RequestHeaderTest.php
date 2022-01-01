<?php

use PHPUnit\Framework\TestCase;
use Wumvi\Utils\Request;

class RequestHeaderTest extends TestCase
{
    public function testIsPost(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertTrue(Request::isPost(), 'Is post request');
    }

    public function testHost(): void
    {
        $host = 'ya.ru';
        $_SERVER['HTTP_HOST'] = $host;
        $this->assertEquals(Request::host(), $host, 'Get host');
    }

    public function testContentType(): void
    {
        $contentType = 'js';
        $_SERVER['CONTENT_TYPE'] = $contentType;
        $this->assertEquals(Request::contentType(), $contentType, 'Get content type');
    }

    public function testContentLenght(): void
    {
        $contentLength = 123;
        $_SERVER['CONTENT_LENGTH'] = $contentLength;
        $this->assertEquals(Request::contentLength(), $contentLength, 'Get content length');
    }

    public function testMethod(): void
    {
        $method = 'POST';
        $_SERVER['HTTP_METHOD'] = $method;
        $this->assertEquals(Request::method(), $method, 'Get method');
    }

    public function testProtocol(): void
    {
        $protocol = 'HTTP/1.1';
        $_SERVER['SERVER_PROTOCOL'] = $protocol;
        $this->assertEquals(Request::protocol(), $protocol, 'Get protocol');
    }

    public function testUri(): void
    {
        $uri = '/test/';
        $_SERVER['REQUEST_URI'] = $uri;
        $this->assertEquals(Request::uri(), $uri, 'Get uri');
    }
}