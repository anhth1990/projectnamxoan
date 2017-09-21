<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ShopContactRequest extends Request {

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
            'content' => 'required|string',
            'title' => 'required|string',
        ];
    }

    public function messages() {
        return [
            'content.required' => 'Bạn phải nhập tiêu đề thông báo',
            'title.required' => 'Bạn phải nhập nội dung thông báo',
        ];
    }

}
