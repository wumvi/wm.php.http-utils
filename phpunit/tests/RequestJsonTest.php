<?php

use PHPUnit\Framework\TestCase;
use Wumvi\HttpUtils\Request;

class RequestJsonTest extends TestCase
{
    public function testPostFieldJson(): void
    {
        $_POST['json4'] = '{"value":1}';
        $value = Request::getJsonObject('json4', [], true, false);
        $this->assertEqualsCanonicalizing($value, ['value' => 1], 'Check json');

        $value = Request::getJsonObject('json-not-exists', [], true, false);
        $this->assertEqualsCanonicalizing($value, [], 'Check default json');

        $_POST['json5'] = '{"value":1}';
        $model = Request::getJsonModel(RequestTestModel::class, 'json5', true, false);
        $this->assertEquals($model->getValue(), 1, 'Check field json model');

        $_POST['json3'] = 'wrong:json';
        $value = Request::getJsonObject('json3', null, true,  false);
        $this->assertNull($value, 'Check wrong json');
    }

    public function testGetJson(): void
    {
        $_GET['json15'] = base64_encode('{"value":1}');
        $value = Request::getJsonObject('json15', [], true);
        var_dump($value);
        $this->assertEqualsCanonicalizing($value, ['value' => 1], 'get json');

        $_GET['json16'] = base64_encode('{"value":1}');
        $value = Request::getJsonObject('get-json-not-exists', null, true);
        $this->assertEqualsCanonicalizing($value, null, 'get json default');

        $_GET['json17'] = base64_encode('wrong-json');
        $value = Request::getJsonObject('json17', null, true);
        $this->assertNull($value, 'get json wrong');
    }
}
