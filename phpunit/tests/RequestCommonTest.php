<?php

use PHPUnit\Framework\TestCase;
use Wumvi\Utils\Request;

class RequestCommonTest extends TestCase
{
    public function testGet(): void
    {
        $data = '123';
        $value = Request::get('data', $data);
        $this->assertEquals($value, $data, 'Check default GET param');

        $data = '456';
        $_GET['check7'] = $data;
        $value = Request::get('check7');
        $this->assertEquals($value, $data, 'Get GET param');
    }

    public function testCookie(): void
    {
        $data = '456';
        $_COOKIE['cookie1'] = $data;
        $value = Request::cookie('cookie1');
        $this->assertEquals('456', $value, 'Get Cookie param');

        $value = Request::cookie('cookie-not-exists', 'ddd');
        $this->assertEquals('ddd', $value, 'Get Cookie param default');
    }

    public function testPost(): void
    {
        $data = '123';
        $value = Request::post('post-not-exist-1', $data);
        $this->assertEquals($value, $data, 'Check default POST param');

        $data = '456';
        $_POST['check8'] = $data;
        $value = Request::post('check8');
        $this->assertEquals($value, $data, 'Get POST param');
    }

    public function testInt(): void
    {
        $data = 123;
        $value = Request::get('post-not-exists-3', $data);
        $this->assertEquals($value, $data, 'Check default int param');

        $data = 456;
        $_GET['check9'] = $data . '';
        $value = Request::getInt('check9');
        $this->assertEquals($value, $data, 'Get GET int param');

        $data = 123;
        $value = Request::postInt('post-not-exists-2', $data);
        $this->assertEquals($value, $data, 'Check default POST int param');

        $data = 456;
        $_POST['check10'] = $data . '';
        $value = Request::postInt('check10');
        $this->assertEquals($value, $data, 'POST int param');
    }

    public function testBool(): void
    {
        $_POST['is-done-post11'] = '1';
        $value = Request::postBool('is-done-post11');
        $this->assertTrue($value, 'Check POST bool param');

        $_GET['is-done-get12'] = '1';
        $value = Request::getBool('is-done-get12');
        $this->assertTrue($value, 'Check GET bool param');
    }
}