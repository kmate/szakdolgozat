<?php

namespace app\model;

class User
{
    const FIELD_ID        = 'id';
    const FIELD_NAME      = 'name';
    const FIELD_FULL_NAME = 'full_name';
    const FIELD_EMAIL     = 'email';
    const FIELD_PASSWORD  = 'password';
    
    public $id;
    public $name;
    public $full_name;
    public $email;
    public $password;
    
    public static function create($id, $name, $fullName, $email, $password)
    {
        $user            = new User();
        $user->id        = $id;
        $user->name      = $name;
        $user->full_name = $fullName;
        $user->email     = $email;
        $user->password  = $password;
        
        return $user;
    }
}