<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class TemplateSnapshot extends Model
{
    public $table = 'template_snapshot';
    public $timestamps = false;

    /**
     * 提取可用列
     * @param $id
     * @return array ['name'=>'value']
     */
    static function fetch_cols($id)
    {
        $cols = [];
        $tpl = DB::table('template_snapshot')->where('id', $id)->first();
        if ($tpl) {
            foreach ($tpl as $key => $val) {
                if (!empty($val) && preg_match("/^c\d*$/", $key)) {
                    $cols[$key] = $val;
                }
            }
        }
        return $cols;
    }
}
