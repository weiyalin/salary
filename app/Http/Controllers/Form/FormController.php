<?php

namespace App\Http\Controllers\Form;

use Storage;
use App\Models\Form;
use App\Models\Template;
use App\Models\Component;
use App\Models\FormGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FormController extends Controller
{
    /**
     * 获取登录用户的表单列表
     * @return JSON
     */
    public function get_list(Request $request)
    {
        $page = $request->input('page');
        $limit = $request->input('limit');
        $group_id = $request->input('group_id');

        $forms = Form::select(['id', 'form_name', 'is_top', 'is_follow', 'group_id', 'create_time', 'status', 'feedback_count', 'url_code', 'view_count'])
            ->where('user_id', get_user_id())
            ->orderBy('is_top', 'desc')
            ->orderBy('id');

        if ($group_id > 0)
        {
            $forms->where('group_id', $group_id);
        }

        $forms = $forms->paginate($limit);

        return [
            'success' => true,
            'forms' => $forms
        ];
    }

    /**
     * 获取登录用户的分组列表
     * @return JSON
     */
    public function get_group_list(Request $request)
    {
        $group_id = $request->input('group_id');

        return [
            'success' => true,
            'form_group' => FormGroup::where('user_id', get_user_id())->get()
        ];
    }

    /**
     * 创建分组
     * @return JSON
     */
    public function group_create(Request $request)
    {
        $name = $request->input('name');

        $form_group = new FormGroup;
        $form_group->name = $name;
        $form_group->user_id = get_user_id();
        $form_group->create_time = ceil(microtime(true) * 1000);
        $form_group->save();

        return [
            'success' => true,
            'message' => '创建成功'
        ];
    }

    public function form_group_edit(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');

        $group = FormGroup::find($id);
        $group->name = $name;
        $group->save();

        return [
            'success' => true,
            'message' => '保存成功'
        ];
    }

    /**
     * 删除分组
     * @return JSON
     */
    public function group_delete(Request $request)
    {
        $id = $request->input('id');

        FormGroup::destroy($id);
        Form::where('group_id', $id)
            ->update(['group_id' => 0]);

        return [
            'success' => true,
            'message' => '删除成功'
        ];
    }

    /**
     * 获取表单的详细信息
     * @return JSON
     */
    public function detail(Request $request)
    {
        return Form::where('id', $request->input('id'))
                ->where('user_id', get_user_id())
                ->first();
    }

    /**
     * 创建表单
     * @return JSON
     */
    public function create(Request $request)
    {
        $form_data = $request->input('form');

        if (empty($form_data['name']))
        {
            return [
                'success' => false,
                'message' => '需要表单名称'
            ];
        }

        $form = new Form;
        $form->form_name = $form_data['name'];
        $form->user_id = get_user_id();
        $form->is_top = $form_data['is_top'] ? 1 : 0;
        $form->is_follow = $form_data['is_follow'] ? 1 : 0;
        $form->create_time = ceil(microtime(true) * 1000);
        $form->url_code = str_random(16);
        $form->save();

        return [
            'success' => true,
            'message' => '创建表单成功',
            'form' => $form
        ];
    }

    /**
     * 保存表单的 Schema
     * @return JSON
     */
    public function save(Request $request)
    {
        $id = $request->input('id');
        $schema = $request->input('schema');
        $form_data = $request->input('form');
        $user = session('user');

        $form = Form::find($id);

        if (!$form || $user->id != $form->user_id) {
            return [
                'success' => false,
                'message' => '表单不存在'
            ];
        }

        $form->form_name = $form_data['form_name'];
        $form->is_follow = $form_data['is_follow'];
        $form->is_top = $form_data['is_top'];
        $form->form_schema = json_encode($schema);
        $form->save();

        return [
            'success' => true,
            'message' => '保存成功'
        ];
    }

    /**
     * 获取 Components
     * @return JSON
     */
    public function components(Request $request)
    {
        return Component::all();
    }

    /**
     * 上传文件，返回 URL
     * @return JSON
     */
    public function upload(Request $request)
    {
        if ($request->file('file') && $request->file('file')->isValid())
        {

            $path = $request->file('file')->store('public');

            return [
                'success' => true,
                'path' => "/form/download?name={$path}"
            ];
        }

        return [
            'success' => false,
            'message' => '上传错误'
        ];
    }

    /**
     * 获取上传的文件
     * @return JSON
     */
    public function download(Request $request)
    {
        $name = $request->input('name');

        if (Storage::exists($name))
        {
            return response()->file(storage_path('app'.DIRECTORY_SEPARATOR.$name));
        }
    }

    /**
     * 创建模版
     * @return JSON
     */
    public function create_template(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $user = session('user');

        $form = Form::where('user_id', $user->id)->find($id);
        if (empty($id) || empty($form))
        {
            return [
                'success' => false,
                'message' => '表单不存在'
            ];
        }

        $template = new Template;
        $template->name = $name;
        $template->demo_pic = '';
        $template->schema = $form->form_schema;
        $template->create_time = ceil(microtime(true) * 1000);
        $template->save();

        return [
            'success' => true,
            'message' => '创建成功'
        ];
    }

    /**
     * 编辑表单属性
     * @return JSON
     */
    public function edit(Request $request)
    {
        $id = $request->input('id');
        $data = $request->input('form');
        $user = session('user');

        $form = Form::where('user_id', $user->id)->find($id);
        if (empty($id) || empty($form))
        {
            return [
                'success' => false,
                'message' => '表单不存在'
            ];
        }

        $form->form_name = $data['form_name'];
        $form->group_id = $data['group_id'];
        $form->is_top = $data['is_top'];
        $form->is_follow = $data['is_follow'];
        $form->save();

        return [
            'success' => true,
            'message' => '保存成功'
        ];
    }
}