<?php

namespace Caramel\Model;

class User extends Model
{

    protected static $table = 'users';

    public string $username;
    public string $email;
    public string $password;

    protected array $fields = [
        'username', 'email', 'password'
    ];

}