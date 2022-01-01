<?php

use PHPUnit\Framework\TestCase;
use Wumvi\Utils\Request;

$fileGetContentsValue = '';

class RequestPostBodyTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        uopz_set_return('file_get_contents', function ($file) {
            global $fileGetContentsValue;
            return $file === 'php://input' ? $fileGetContentsValue : file_get_contents($file);
        }, true);
    }

    public static function tearDownAfterClass(): void
    {
        uopz_unset_return('file_get_contents');
    }

    public function testPostBodyJsonModel(): void
    {
        global $fileGetContentsValue;
        $fileGetContentsValue = '{"value":2}';
        $model = Request::postBodyJsonModel(RequestTestModel::class);
        $this->assertEqualsCanonicalizing($model->getValue(), 2, 'post body json model');

        $fileGetContentsValue = 'wrong:json';
        $value = Request::postBodyJsonModel(RequestTestModel::class);
        $this->assertEqualsCanonicalizing($value, null, 'post body wrong json');
    }

    public function testPostBodyJwtModel(): void
    {
        // use https://jwt.io/ for test
        global $fileGetContentsValue;
        $fileGetContentsValue = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ2YWx1ZSI6M30.X5vHVyl40H6eiY5yzqnVwMUW6hOe7-UjEl5_eoc79g0';
        $model = Request::postBodyJwtModel(RequestTestModel::class, 'pwdtest');
        $this->assertEqualsCanonicalizing($model->getValue(), 3, 'post body jwt model');

        $fileGetContentsValue = 'wrong:json';
        $value = Request::postBodyJwtModel(RequestTestModel::class, 'pwdtest');
        $this->assertEqualsCanonicalizing($value, null, 'post body wrong jwt model');
    }
}
