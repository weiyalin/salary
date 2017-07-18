<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

/**
 * 试卷分析
 * Class Analysis
 * @package App\Models
 */
class Analysis extends Model
{
    //
    public static function lists($term_id,$course_id,$paper_name,$per_page=10){
        $query = DB::table('exam');
        $query->where('is_delete',0);
        if($term_id){
            $query->where('term_id',$term_id);
        }
        if($course_id){
            $query->where('course_id',$course_id);
        }
        if($paper_name){
            $query->where('exam_paper_name','like',$paper_name.'%');
//            $query->where(function($q) use($paper_name){
//                $q->orWhere('name','like',$paper_name.'%')
//                    ->orWhere('name_quanpin','like',$paper_name.'%')
//                    ->orWhere('name_jianpin','like',$paper_name.'%');
//            });
        }

        $query->orderBy('create_time','desc');

        $results = $query->paginate($per_page);
        return responseToPage($results);
    }

    public static function stat($exam_id){
        //考试分数统计:最高分,最低分,平均分,标准差
        $exam = DB::table('exam')->where('id',$exam_id)->first();
//        $max_score = DB::table('student_paper')->where('exam_id',$exam_id)->max('score');
//        $min_score = DB::table('student_paper')->where('exam_id',$exam_id)->min('score');
//        $avg_score = DB::table('student_paper')->where('exam_id',$exam_id)->avg('score');
//        $std_score = DB::table('student_paper')
//            ->select(DB::raw('FORMAT(STD(score),2) as std_score'))
//            ->where('exam_id',$exam_id)
//            ->value('std_score');
        $max = $exam->max_score > -1 ? $exam->max_score : '-';
        $min = $exam->min_score > -1 ? $exam->min_score :  '-';
        $avg = $exam->avg_score > -1 ? $exam->avg_score : '-';
        $std = $exam->std_score > -1 ? $exam->std_score : '-';

        $exam_list = [['max'=>$max,'min'=>$min,'avg'=>$avg,'std'=>$std]];

        //考生成绩分布段: 优秀(>=90),良好(>=70),中等(>=60),不及格(<60)
        $grade_list = DB::table('stat_grade')->where('exam_id',$exam_id)->get();

        //难易题目的分值占总比例(难度等级id):
        $level_list = DB::table('stat_question_level')->where('exam_id',$exam_id)->get();

        //各种题型分值占比
        $type_list = DB::table('stat_question_type')->where('exam_id',$exam_id)->get();

        $result = ['exam'=>$exam,'exam_list'=>$exam_list,'grade_list'=>$grade_list,'level_list'=>$level_list,'type_list'=>$type_list];
        return responseToJson(0,'success',$result);
    }

}
