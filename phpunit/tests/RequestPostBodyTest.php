<?php

use PHPUnit\Framework\TestCase;
use Wumvi\HttpUtils\Request;

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
}
