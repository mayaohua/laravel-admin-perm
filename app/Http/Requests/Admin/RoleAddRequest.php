<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RoleAddRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }



    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'show_name' => 'required|between:2,50|unique:roles',
            'name' => 'required|between:2,50|unique:roles|my_alpha',
            'permissions' => 'array|min:1'
            //                'regex:/^1[34578][0-9]\d{4,8}|(\w)+(\.\w+)*@(\w)+((\.\w+)+)|[0-9a-zA-Z_]+$/',//验证为手机号，邮箱，或帐号

        ];
    }



    public function attributes(){

        return [
            'show_name'   => '角色名称',
            'name'        => '操作名称',
            'permissions' => '角色权限'
        ];
    }
}
