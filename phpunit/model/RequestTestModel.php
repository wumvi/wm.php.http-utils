<?php

class RequestTestModel
{
    public function __construct(public array $raw)
    {
    }

    public function getValue(): int
    {
        return $this->raw['value'];
    }
}
