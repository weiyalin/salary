<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Models\Role;
use Log;

class UserOrg extends Model
{
    public $table = 'user_org';
    public $timestamps = false;
}
