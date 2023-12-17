<?php
declare(strict_types=1);

namespace Wumvi\HttpUtils;

class Cookie {
    /**
     * @param string $name
     * @param string $default
     *
     * @return string
     */
    public static function get(string $name, string $default = ''): string
    {
        return $_COOKIE[$name] ?? $default;
    }
}
