<?php
declare(strict_types=1);

namespace Wumvi\HttpUtils;

class SidRequest
{
    public const string SID_KEY = JWT::TOKEN_KEY;
    public const string SID_HEADER = JWT::TOKEN_HEADER;
    private readonly JWT $jwt;

    public function __construct(JWT $jwt)
    {
        $this->jwt = $jwt;
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $model
     * @param bool $isCheckPrivateIp
     *
     * @return T|object
     * @throws \Exception
     */
    public function get(string $model, bool $isCheckPrivateIp = false): object
    {
        return $this->jwt->getToken($model, $isCheckPrivateIp, self::SID_KEY, self::SID_HEADER);
    }
}
