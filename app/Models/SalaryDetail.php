<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryDetail extends Model
{
    public $table = 'salary_detail';
    public $timestamps = false;

    /**
     * 设置条件年
     * @param $query
     * @param $year
     * @return mixed
     */
    public function scopeYear($query, $year)
    {
        return $query->where('send_year', $year);
    }

    /**
     * 设置条件工号
     * @param $query
     * @param $code
     * @return mixed
     */
    public function scopeCode($query, $code)
    {
        return $query->where('code', $code);
    }

    public function template()
    {
        return $this->hasOne('App\Models\TemplateSnapshot', 'id', 'tpl_snapshot_id');
    }
}
