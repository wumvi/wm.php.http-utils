<?php
declare(strict_types=1);

namespace Wumvi\HttpUtils;

use Firebase\JWT\JWT as FirebaseJWT;
use Firebase\JWT\Key;

class JWT
{
    private const string ALGS = 'HS256';
    public const string TOKEN_KEY = 'token';
    public const string TOKEN_HEADER = 'TOKEN';
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
     * @param string $key
     * @param string $header
     *
     * @return T
     * @throws \Exception
     */
    public function getToken(
        string $model,
        bool $isCheckPrivateIp = false,
        string $key = self::TOKEN_KEY,
        string $header = self::TOKEN_HEADER
    ) {
        $jwtRaw = $_POST[$key] ?? $_GET[$key] ?? $_SERVER['HTTP_' . $header] ?? '';
        if ($jwtRaw === '') {
            throw new \Exception('jwt-not-found');
        }

        $result = $this->decodePrivate($model, $jwtRaw, $isCheckPrivateIp);
        return $result ?: $this->decode($model, $jwtRaw);
    }

    /**
     * @template T of \stdClass
     *
     * @param class-string<T> $model
     * @param string $jwtRaw
     *
     * @return T
     * @throws \Exception
     */
    public function decode(string $model, string $jwtRaw)
    {
        return new $model(FirebaseJWT::decode($jwtRaw, $this->keys));
    }

    public function decodePrivate(string $model, string $jwtRaw, bool $isCheckPrivateIp)
    {
        if (!$isCheckPrivateIp) {
            return null;
        }
        $tks = explode('.', $jwtRaw);
        if (count($tks) === 3 && IpUtils::isPrivateIp($_SERVER['REMOTE_ADDR'])) {
            list($headb64, $bodyB64, $cryptob64) = $tks;
            $data = FirebaseJWT::urlsafeB64Decode($bodyB64);
            $data = FirebaseJWT::jsonDecode($data);
            return new $model($data);
        }

        return null;
    }

    public function encode($data, string $key, ?int $exp = null): string
    {
        if (!array_key_exists($key, $this->keys)) {
            throw new \Exception('key-not-found: ' . $key);
        }

        $keyCode = $this->keys[$key]->getKeyMaterial();
        $algo = $this->keys[$key]->getAlgorithm();

        if ($exp !== null) {
            $data['exp'] = $exp;
        }

        return FirebaseJWT::encode($data, $keyCode, $algo, $key);
    }
}