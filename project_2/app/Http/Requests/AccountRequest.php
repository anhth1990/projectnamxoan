<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class AccountRequest extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'old_pass' => 'required|min:6',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|min:6|same:password',
        ];
    }

    public function messages() {
        return [
            'old_pass.required' => 'Bạn phải nhập mật khẩu đang sử dụng',
            'old_pass.min' => 'Mật khẩu đang sử dụng từ 6 ký tự trở lên',
			'password.required' => 'Bạn phải nhập mật khẩu mới',
            'password.min' => 'Mật khẩu mới từ 6 ký tự trở lên',
			'password_confirmation.required' => 'Bạn phải nhập lại mật khẩu mới',
            'password_confirmation.min' => 'Mật khẩu mới từ 6 ký tự trở lên',
            'password_confirmation.same' => 'Mật khẩu phải trùng nhau',
        ];
    }

}
