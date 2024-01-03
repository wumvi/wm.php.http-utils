<?php
declare(strict_types=1);

namespace Wumvi\HttpUtils;

/**
 *
 */
class SidJwtModel extends \stdClass
{
    protected \stdClass $raw;

    public function __construct(\stdClass $raw)
    {
        $this->raw = $raw;
    }

    public function getClientId(): int
    {
        return $this->raw->cid;
    }

    public function getUserId(): int
    {
        return $this->raw->uid;
    }

    public function getSessionId(): int
    {
        return $this->raw->sid;
    }
}
