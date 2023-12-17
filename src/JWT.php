<?php
declare(strict_types=1);

namespace Wumvi\HttpUtils;

use Firebase\JWT\JWT as FirebaseJWT;
use Firebase\JWT\Key;

class JWT
{
    private const string ALGS = 'HS256';
    public const string JWT_KEY = 'jwt';
    public const string JWT_HEADER = 'JWT';
    private array $keys;

    public function __construct(array $keys)
    {
        $this->keys = array_map(fn($key) => new Key($key, self::ALGS), $keys);
    }

    /**
     * @template T of \stdClass
     *
     * @param class-string<T> $model
     * @param bool $isCheckPrivateIp
     * @param string $post
     * @param string $get
     * @param string $header
     *
     * @return T
     * @throws \Exception
     */
    public function get(
        string $model,
        bool $isCheckPrivateIp = false,
        string $post = self::JWT_KEY,
        string $get = self::JWT_KEY,
        string $header = self::JWT_HEADER
    ): \stdClass
    {
        $jwtRaw = $_POST[$post] ?? $_GET[$get] ?? $_SERVER['HTTP_' . $header] ?? '';
        if ($jwtRaw === '') {
            throw new \Exception('jwt not found');
        }

        $tks = explode('.', $jwtRaw);
        if (count($tks) === 3 && IpUtils::isPrivateIp($_SERVER['REMOTE_ADDR']) && $isCheckPrivateIp) {
            list($headb64, $bodyb64, $cryptob64) = $tks;
            $data = FirebaseJWT::urlsafeB64Decode($bodyb64);
            $data = FirebaseJWT::jsonDecode($data);

            return new $model($data);
        }

        $data = FirebaseJWT::decode($jwtRaw, $this->keys);

        return new $model($data);
    }

    public function encode($data, $key): string
    {
        if (!array_key_exists($key, $this->keys)) {
            throw new \Exception('key ' . $key . ' not found');
        }

        return FirebaseJWT::encode($data, $this->keys[$key], self::ALGS);
    }
}