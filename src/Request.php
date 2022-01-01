<?php
declare(strict_types=1);

namespace Wumvi\Utils;

use Exception;

/**
 * Получение переменных и работы с массивами GET или POST
 */
class Request
{
    public const DEFAULT_JWT_OPTIONS = ['algorithm' => 'HS256'];

    /**
     * Возвращает GET переменную
     *
     * @param string $name Название переменной
     * @param string $default Значение по умолчанию, если переменной нет
     *
     * @return string Значение переменной
     */
    public static function get(string $name, string $default = ''): string
    {
        return $_GET[$name] ?? $default;
    }

    /**
     * Возвращает значение переменной из GET массива
     *
     * @param string $name название переменной
     * @param integer $default значение по умолчанию, если переменной нет или опеределена как пустая
     *
     * @return integer значение переменной
     */
    public static function getInt(string $name, int $default = 0): int
    {
        return (int)($_GET[$name] ?? $default);
    }

    /**
     * Возвращает POST переменную
     *
     * @param string $name Название параметра
     * @param string $default Значение по умолчанию, если переменной нет
     *
     * @return string Значение
     */
    public static function post(string $name, string $default = ''): string
    {
        return $_POST[$name] ?? $default;
    }

    /**
     * Возвращает значение переменной из POST массива и преобразует в int
     *
     * @param string $name Название переменной
     * @param integer $default Значение по умолчанию, если переменной нет или определена как пустая
     *
     * @return integer Значение переменной
     */
    public static function postInt(string $name, int $default = 0): int
    {
        return (int)($_POST[$name] ?? $default);
    }

    public static function getBool(string $name): bool
    {
        return ($_GET[$name] ?? '0') === '1';
    }

    public static function postBool(string $name): bool
    {
        return ($_POST[$name] ?? '0') === '1';
    }

    /**
     * @return string
     */
    public static function postBody(): string
    {
        return file_get_contents('php://input') ?: '';
    }

    /**
     * @param ?array $default
     * @param bool|null $associative
     * @param int<1, max> $depth [optional]
     * @param int $flags
     *
     * @return ?array
     */
    public static function postBodyJson(
        ?array $default = [],
        ?bool $associative = false,
        int $depth = 512,
        int $flags = JSON_THROW_ON_ERROR
    ): ?array {
        $data = file_get_contents('php://input');

        try {
            /** @throws Exception */
            return empty($data) ? $default : json_decode($data, $associative, $depth, $flags);
        } catch (\Throwable $ex) {
            return null;
        }
    }

    /**
     *
     * @param ?array $default
     * @param string $name
     * @param bool|null $associative
     * @param int<1, max> $depth [optional]
     * @param int $flags [optional]
     *
     * @return array|object|null
     */
    public static function postFieldJson(
        string $name,
        ?array $default = [],
        ?bool $associative = false,
        int $depth = 512,
        int $flags = JSON_THROW_ON_ERROR
    ): array|object|null {
        try {
            return array_key_exists($name, $_POST)
                ? json_decode($_POST[$name], $associative, $depth, $flags)
                : $default;
        } catch (\Throwable $ex) {
            return null;
        }
    }

    /**
     *
     * @param ?array $default
     * @param string $name
     * @param bool|null $associative
     * @param bool $isBase64
     * @param int<1, max> $depth [optional]
     * @param int $flags [optional]
     *
     * @return array|object|null
     */
    public static function getJson(
        string $name,
        ?array $default = [],
        ?bool $associative = false,
        bool $isBase64 = true,
        int $depth = 512,
        int $flags = JSON_THROW_ON_ERROR
    ): array|object|null {
        if (!array_key_exists($name, $_GET)) {
            return $default;
        }
        try {
            $data = $isBase64 ? base64_decode($_GET[$name]) : $_GET[$name];

            return json_decode($data, $associative, $depth, $flags);
        } catch (\Throwable $ex) {
            return null;
        }
    }

    /**
     * @template T of \stdClass
     *
     * @param class-string<T> $model
     * @param string|null $name
     * @param bool|null $associative
     * @param int<1, max> $depth
     * @param int $flags
     *
     * @return ?T
     */
    public static function postFieldJsonModel(
        string $model,
        ?string $name = null,
        ?bool $associative = true,
        int $depth = 512,
        int $flags = JSON_THROW_ON_ERROR
    ): ?object {
        $data = self::postFieldJson($name, null, $associative, $depth, $flags);

        return empty($data) ? null : new $model($data);
    }

    /**
     * @template T of \stdClass
     *
     * @param class-string<T> $model
     * @param bool|null $associative
     * @param int<1, max> $depth
     * @param int $flags
     *
     * @return ?T
     */
    public static function postBodyJsonModel(
        string $model,
        ?bool $associative = true,
        int $depth = 512,
        int $flags = JSON_THROW_ON_ERROR
    ): ?object {
        $data = self::postBodyJson(null, $associative, $depth, $flags);

        return empty($data) ? null : new $model($data);
    }

    /**
     * @param string $key
     * @param array $options
     *
     * @return ?array
     */
    public static function postBodyJwt(string $key, array $options = self::DEFAULT_JWT_OPTIONS): ?array
    {
        return jwt_decode(self::postBody(), $key, $options);
    }

    /**
     * @param string $name
     * @param string $key
     * @param array $options
     *
     * @return ?array
     */
    public static function postFieldJwt(string $name, string $key, array $options = self::DEFAULT_JWT_OPTIONS): ?array
    {
        return jwt_decode(self::post($name), $key, $options);
    }

    /**
     * @template T of \stdClass
     *
     * @param class-string<T> $model
     * @param string $name
     * @param string $key
     * @param array $options
     *
     * @return ?T
     */
    public static function postFieldJwtModel(
        string $model,
        string $name,
        string $key,
        array $options = self::DEFAULT_JWT_OPTIONS
    ): ?object {
        $data = self::postFieldJwt($name, $key, $options);

        return empty($data) ? null : new $model($data);
    }

    /**
     * @template T of \stdClass
     *
     * @param class-string<T> $model
     * @param string $key
     * @param array $options
     *
     * @return ?T
     */
    public static function postBodyJwtModel(
        string $model,
        string $key,
        array $options = self::DEFAULT_JWT_OPTIONS
    ): ?object {
        $data = self::postBodyJwt($key, $options);

        return empty($data) ? null : new $model($data);
    }

    /**
     * @param string $name
     * @param string $key
     * @param array $options
     *
     * @return ?array
     */
    public static function getJwt(string $name, string $key, array $options = self::DEFAULT_JWT_OPTIONS): ?array
    {
        return jwt_decode(self::get($name), $key, $options);
    }

    /**
     * @template T of \stdClass
     *
     * @param class-string<T> $model
     * @param string $name
     * @param string $key
     * @param array $options
     *
     * @return ?T
     */
    public static function getJwtModel(
        string $model,
        string $name,
        string $key,
        array $options = self::DEFAULT_JWT_OPTIONS
    ): ?object {
        $data = self::getJwt($name, $key, $options);

        return empty($data) ? null : new $model($data);
    }

    /**
     * @param string $name
     * @param string $key
     * @param array $options
     *
     * @return ?array
     */
    public static function headerJwt(string $name, string $key, array $options = self::DEFAULT_JWT_OPTIONS): ?array
    {
        return jwt_decode(self::header($name), $key, $options);
    }

    /**
     * @template T of \stdClass
     *
     * @param class-string<T> $model
     * @param string $name
     * @param string $key
     * @param array $options
     *
     * @return ?T
     */
    public static function headerJwtModel(
        string $model,
        string $name,
        string $key,
        array $options = self::DEFAULT_JWT_OPTIONS
    ): ?object {
        $data = self::headerJwt($name, $key, $options);

        return empty($data) ? null : new $model($data);
    }

    public static function jwt(
        string $name,
        string $key,
        array $options = self::DEFAULT_JWT_OPTIONS
    ): ?array {
        $jwt = self::header($name) ?: self::post($name) ?: self::get($name);

        return empty($jwt) ? null : jwt_decode($jwt, $key, $options);
    }

    public static function jwtModel(
        string $model,
        string $name,
        string $key,
        array $options = self::DEFAULT_JWT_OPTIONS
    ): ?object {
        $data = self::jwt($name, $key, $options);

        return empty($data) ? null : new $model($data);
    }

    public static function header(string $name): string
    {
        return $_SERVER['HTTP_' . strtoupper($name)] ?? '';
    }

    public static function uri(): string
    {
        return $_SERVER['REQUEST_URI'];
    }

    public static function protocol(): string
    {
        return $_SERVER['SERVER_PROTOCOL'] ?? '';
    }

    public static function contentType(): string
    {
        return $_SERVER["CONTENT_TYPE"] ?? '';
    }

    public static function contentLength(): int
    {
        return (int)($_SERVER['CONTENT_LENGTH'] ?? 0);
    }


    public static function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Возвращает TRUE, если запрос типа POST, иначе FALSE
     *
     * @return bool Post запрос это или нет
     */
    public static function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Возвращает хост
     *
     * @return string хост
     */
    public static function host(): string
    {
        return $_SERVER['HTTP_HOST'];
    }
}
