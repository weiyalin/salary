<?php
/**
 * Created by PhpStorm.
 * User: weiyalin
 * Date: 2017/7/18
 * Time: 13:03
 */

namespace App\Http\Controllers\Finance;


use App\Http\Controllers\Controller;
use App\Models\Apply;
use Illuminate\Http\Request;

class ApplyController extends Controller
{
    function lists(Request $request)
    {
        $pageSize = $request->page_size;
        $keyword = trim($request->keyword);
        $status = intval($request->status);

        return responseToJson(0, 'success', Apply::search_apply([
            'page_size' => $pageSize ? $pageSize : 50,
            'keyword' => $keyword,
            'status' => $status
        ]));
    }

    function options(){
        return responseToJson(0, 'success', Apply::options());
    }

    function delete(Request $request){
        $salaryId = intval($request->id);
        Apply::delete_apply($salaryId);
        return responseToJson(0, 'success');
    }
}