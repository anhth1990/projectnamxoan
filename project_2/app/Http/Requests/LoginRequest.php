<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class LoginRequest extends Request
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
            'username' => 'required|max:30',
            'password' => 'required',
			//'username' => 'required|unique:bn_shops|max:20',
            //'password' => 'required|unique:bn_shops',
        ];
    }
	public function messages()
	{
		return [
			'username.required' => 'Bạn phải nhập tên đăng nhập',
			'username.max' => 'Tên đăng nhập không quá 20 ký tự',
			'password.required'  => 'Bạn phải nhập mật khẩu',
		];
	}
}
