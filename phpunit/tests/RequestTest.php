<?php

use PHPUnit\Framework\TestCase;
use Wumvi\Utils\Request;

class RequestTest extends TestCase
{
    public function testGet(): void
    {
        $data = '123';
        $value = Request::get('data', $data);
        $this->assertEquals($value, $data, 'Check default GET param');

        $data = '456';
        $_GET['check'] = $data;
        $value = Request::get('check');
        $this->assertEquals($value, $data, 'Get GET param');
    }

    public function testGetInt(): void
    {
        $data = 123;
        $value = Request::get('data', $data);
        $this->assertEquals($value, $data, 'Check default int param');

        $data = 456;
        $_GET['check'] = $data . '';
        $value = Request::getInt('check');
        $this->assertEquals($value, $data, 'Get GET int param');
    }

    public function testPost(): void
    {
        $data = '123';
        $value = Request::post('data', $data);
        $this->assertEquals($value, $data, 'Check default POST param');

        $data = '456';
        $_POST['check'] = $data;
        $value = Request::post('check');
        $this->assertEquals($value, $data, 'Get POST param');
    }

    public function testPostInt(): void
    {
        $data = 123;
        $value = Request::postInt('data', $data);
        $this->assertEquals($value, $data, 'Check default POST int param');

        $data = 456;
        $_POST['check'] = $data . '';
        $value = Request::postInt('check');
        $this->assertEquals($value, $data, 'Get POST int param');
    }

    public function testIsPost(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertTrue(Request::isPost(), 'Is post request');
    }

    public function testHost(): void
    {
        $host = 'ya.ru';
        $_SERVER['HTTP_HOST'] = $host;
        $this->assertEquals(Request::getHost(), $host, 'Get host');
    }
}