<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Response;
use App\Models\Menu;

class MenuController extends Controller
{
    //
    function get_menu(){
        return Response::json(Menu::get_format_menu());
    }

    function get_role_menu(){
        return Response::json(Menu::get_format_role_menu());
    }
}
