<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ContactRequest extends Request {

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
            'content' => 'required',
            'c_id' => 'required',
        ];
    }

    public function messages() {
        return [
            'content.required' => 'Bạn phải nhập nội dung trả lời',
            'c_id.required' => 'Error',
        ];
    }

}
