<?php

namespace App\Helpers;

use Illuminate\Foundation\Auth\User as Authenticatable;

class UserLogin extends Authenticatable {
    public int $_id;
    public string $_name;

    public function __construct(int $id, string $name)
    {
        $this->_id = $id;
        $this->_name = $name;
    }

    public function getId(): int
    {
        return $this->_id;
    }

    public function getName(): string
    {
        return $this->_name;
    }

    public static function login(): UserLogin
    {
        return new UserLogin('144000', 'Esperança');
    }
}
