<?php

use PHPUnit\Framework\TestCase;
use Wumvi\Utils\Request;

class RequestJwtTest extends TestCase
{
    public $jwt = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ2YWx1ZSI6M30.X5vHVyl40H6eiY5yzqnVwMUW6hOe7-UjEl5_eoc79g0';

    public function testPostFieldJwt(): void
    {
        $_POST['jwt-post1'] = $this->jwt;
        $value = Request::postFieldJwt('jwt-post1', 'pwdtest');
        $this->assertEqualsCanonicalizing($value, ['value' => 3], 'post jwt array');

        $model = Request::postFieldJwtModel(RequestTestModel::class, 'jwt-post1', 'pwdtest');
        $this->assertEqualsCanonicalizing($model->getValue(), 3, 'post field jwt model');
    }

    public function testGetJwt(): void
    {
        $_GET['jwt-get2'] = $this->jwt;
        $value = Request::getJwt('jwt-get2', 'pwdtest');
        $this->assertEqualsCanonicalizing($value, ['value' => 3], 'get jwt array');

        $model = Request::getJwtModel(RequestTestModel::class, 'jwt-get2', 'pwdtest');
        $this->assertEqualsCanonicalizing($model->getValue(), 3, 'get jwt model');
    }

    public function testHeaderJwt(): void
    {
        $_SERVER['HTTP_JWT2'] = $this->jwt;
        $value = Request::headerJwt('jwt2', 'pwdtest');
        $this->assertEqualsCanonicalizing($value, ['value' => 3], 'header jwt array');

        $model = Request::headerJwtModel(RequestTestModel::class, 'jwt2', 'pwdtest');
        $this->assertEqualsCanonicalizing($model->getValue(), 3, 'header jwt model');
    }

    public function testCookieJwt(): void
    {
        $_COOKIE['cookie-jwt1'] = $this->jwt;
        $value = Request::cookieJwt('cookie-jwt1', 'pwdtest');
        $this->assertEqualsCanonicalizing($value, ['value' => 3], 'cookie jwt array');

        $model = Request::cookieJwtModel(RequestTestModel::class, 'cookie-jwt1', 'pwdtest');
        $this->assertEqualsCanonicalizing($model->getValue(), 3, 'cookie jwt model');
    }

    public function testUniversalJwt(): void
    {
        $_SERVER['HTTP_U_JWT3'] = $this->jwt;
        $value = Request::jwt('u_jwt3', 'pwdtest');
        $this->assertEqualsCanonicalizing($value, ['value' => 3], 'universal jwt header array');

        $_POST['u-jwt-4'] = $this->jwt;
        $value = Request::jwt('u-jwt-4', 'pwdtest');
        $this->assertEqualsCanonicalizing($value, ['value' => 3], 'universal jwt post array');

        $_GET['u-jwt-5'] = $this->jwt;
        $value = Request::jwt('u-jwt-5', 'pwdtest');
        $this->assertEqualsCanonicalizing($value, ['value' => 3], 'universal jwt get array');

        $_GET['u-jwt-6'] = $this->jwt;
        $model = Request::jwtModel(RequestTestModel::class, 'u-jwt-6', 'pwdtest');
        $this->assertEqualsCanonicalizing($model->getValue(), 3, 'universal jwt model');
    }
}
