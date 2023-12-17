<?php
declare(strict_types=1);

namespace Wumvi\HttpUtils;

class Response
{
    public static function error(string $msg): string
    {
        http_response_code(550);
        return json_encode(['status' => 'error', 'msg' => $msg], JSON_THROW_ON_ERROR);
    }

    public static function ok(array $add = []): string
    {
        return json_encode(['status' => 'ok'] + $add, JSON_THROW_ON_ERROR);
    }

    public static function headerContentTypeJson(): void
    {
        header('Content-Type: application/json; charset=utf-8');
    }

    public static function headerAllowOrigin(): void
    {
        header('Access-Control-Allow-Origin: *');
    }

    public static function check(bool $is, string $error): void
    {
        if ($is) {
            self::headerContentTypeJson();
            die(self::error($error));
        }
    }
}
