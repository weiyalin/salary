<?php

namespace App\Models;


class FormUser
{
    public static function factory(){
        $name = "App\\Models\\".env('FORM_USER','FormUser');
        $reflect_obj = new \ReflectionClass($name);
        $obj = $reflect_obj->newInstance();

        return $obj;
    }

    function is_login(){
        if(empty(get_user_info())){
            return false;
        }
        return true;
    }


    function get_user(){
        return get_user_info();
    }


    function get_openid(){
        return get_user_openid();
    }


}
