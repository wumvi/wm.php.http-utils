<?php
declare(strict_types=1);

namespace Wumvi\HttpUtils;

class SidRequest
{
    public const string SID_KEY = 'sid';
    public const string SID_HEADER = 'SID';
    private readonly JWT $jwt;

    public function __construct(JWT $jwt)
    {
        $this->jwt = $jwt;
    }

    /**
     * @template T of \stdClass
     *
     * @param class-string<T> $model
     * @param bool $isCheckPrivateIp
     *
     * @return T
     * @throws \Exception
     */
    public function get(string $model, bool $isCheckPrivateIp = false)
    {
        return $this->jwt->getToken($model, $isCheckPrivateIp, self::SID_KEY, self::SID_HEADER);
    }
}
