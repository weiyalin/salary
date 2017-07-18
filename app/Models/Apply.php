<?php
/**
 * Created by PhpStorm.
 * User: weiyalin
 * Date: 2017/7/18
 * Time: 13:43
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Apply extends Model
{
    public $table = 'apply';

    static function search_apply($params){

        $pageSize = $params['page_size'];
        $keyword = $params['keyword'];
        $status = $params['status'];
        $query = DB::table('apply');

        if ($keyword) {
            $query->where(function ($query) use ($keyword) {
                $query->where('person', 'like', "%{$keyword}%")
                    ->orWhere('company', 'like', "%{$keyword}%");
            });
        }

        if ($status == 0 || $status == 1) {
            $query->where('status', $status);
        }

        return $query->paginate($pageSize);
    }

    static function options()
    {

        $query = DB::table('apply');

        $data = [];

        $count = $query->count();
        $data[] = ['id' => 2, 'name' => '全部(' . $count . ')'];

        $count_1 = $query->where('status', 0)->count();
        $data[] = ['id' => 0, 'name' => '未处理(' . $count_1 . ')'];

        $query = DB::table('apply');
        $count_2 = $query->where('status', 1)->count();
        $data[] = ['id' => 1, 'name' => '已处理(' . $count_2 . ')'];

        return $data;
    }

    static function delete_apply($apply_id)
    {

    }
}