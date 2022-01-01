<?php

use PHPUnit\Framework\TestCase;
use Wumvi\Utils\Request;

class RequestJsonTest extends TestCase
{
    public function testPostFieldJson(): void
    {
        $_POST['json4'] = '{"value":1}';
        $value = Request::postFieldJson('json4', [], true);
        $this->assertEqualsCanonicalizing($value, ['value' => 1], 'Check json');

        $value = Request::postFieldJson('json-not-exists', [], true);
        $this->assertEqualsCanonicalizing($value, [], 'Check default json');

        $_POST['json5'] = '{"value":1}';
        $model = Request::postFieldJsonModel(RequestTestModel::class, 'json5');
        $this->assertEquals($model->getValue(), 1, 'Check field json model');

        $_POST['json3'] = 'wrong:json';
        $value = Request::postFieldJson('json3', [], true);
        $this->assertNull($value, 'Check wrong json');
    }

    public function testGetJson(): void
    {
        $_GET['json15'] = base64_encode('{"value":1}');
        $value = Request::getJson('json15', [], true);
        $this->assertEqualsCanonicalizing($value, ['value' => 1], 'get json');

        $_GET['json16'] = base64_encode('{"value":1}');
        $value = Request::getJson('get-json-not-exists', [], true);
        $this->assertEqualsCanonicalizing($value, [], 'get json default');

        $_GET['json17'] = base64_encode('wrong-json');
        $value = Request::getJson('json17', [], true);
        $this->assertNull($value, 'get json wrong');
    }
}
