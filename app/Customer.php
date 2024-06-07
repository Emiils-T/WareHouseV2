<?php

namespace App;

use JsonSerializable;

class Customer implements JsonSerializable

{
    private string $name;
    private string $code;
    public function __construct(string $name, string $code)
    {

        $this->name = $name;
        $this->code = $code;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getCode(): string
    {
        return $this->code;
    }
    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'code' => $this->code
        ];
    }

}