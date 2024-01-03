<?php
declare(strict_types=1);

namespace Wumvi\HttpUtils;

class IpUtils
{
    public const array PRIVATE_SUBNETS = [
        '127.0.0.0/8',    // RFC1700 (Loopback)
        '10.0.0.0/8',     // RFC1918
        '192.168.0.0/16', // RFC1918
        '172.16.0.0/12',  // RFC1918
        '169.254.0.0/16', // RFC3927
        '0.0.0.0/8',      // RFC5735
        '240.0.0.0/4',    // RFC1112
    ];

    private static array $checkedIps = [];

    /**
     * This class should not be instantiated.
     */
    private function __construct()
    {
    }

    /**
     * Checks if an IPv4 or IPv6 address is contained in the list of given IPs or subnets.
     *
     * @param string|array $ips List of IPs or subnets (can be a string if only a single one)
     */
    public static function checkIp(string $requestIp, string|array $ips): bool
    {
        if (!\is_array($ips)) {
            $ips = [$ips];
        }

        foreach ($ips as $ip) {
            if (self::checkIp4($requestIp, $ip)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Compares two IPv4 addresses.
     * In case a subnet is given, it checks if it contains the request IP.
     *
     * @param string $ip IPv4 address or subnet in CIDR notation
     *
     * @return bool Whether the request IP matches the IP, or whether the request IP is within the CIDR subnet
     */
    public static function checkIp4(string $requestIp, string $ip): bool
    {
        $cacheKey = $requestIp . '-' . $ip . '-v4';
        if (null !== $cacheValue = self::getCacheResult($cacheKey)) {
            return $cacheValue;
        }

        if (!filter_var($requestIp, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV4)) {
            return self::setCacheResult($cacheKey, false);
        }

        if (str_contains($ip, '/')) {
            [$address, $netmask] = explode('/', $ip, 2);
            $netmask = (int)$netmask;

            if ($netmask === 0) {
                return self::setCacheResult(
                    $cacheKey,
                    filter_var($address, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV4) !== false
                );
            }

            if ($netmask < 0 || $netmask > 32) {
                return self::setCacheResult($cacheKey, false);
            }
        } else {
            $address = $ip;
            $netmask = 32;
        }

        if (false === ip2long($address)) {
            return self::setCacheResult($cacheKey, false);
        }

        $result = substr_compare(
            sprintf('%032b', ip2long($requestIp)),
            sprintf('%032b', ip2long($address)),
            0,
            $netmask
        );
        return self::setCacheResult($cacheKey, $result === 0);
    }

    /**
     * Checks if an IPv4 or IPv6 address is contained in the list of private IP subnets.
     */
    public static function isPrivateIp(string $requestIp): bool
    {
        return self::checkIp($requestIp, self::PRIVATE_SUBNETS);
    }

    private static function getCacheResult(string $cacheKey): ?bool
    {
        if (isset(self::$checkedIps[$cacheKey])) {
            // Move the item last in cache (LRU)
            $value = self::$checkedIps[$cacheKey];
            unset(self::$checkedIps[$cacheKey]);
            self::$checkedIps[$cacheKey] = $value;

            return self::$checkedIps[$cacheKey];
        }

        return null;
    }

    private static function setCacheResult(string $cacheKey, bool $result): bool
    {
        if (1000 < \count(self::$checkedIps)) {
            // stop memory leak if there are many keys
            self::$checkedIps = \array_slice(self::$checkedIps, 500, null, true);
        }

        return self::$checkedIps[$cacheKey] = $result;
    }
}
