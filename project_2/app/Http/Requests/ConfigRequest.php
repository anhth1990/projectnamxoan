<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ConfigRequest extends Request
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
    public function rules() {
        return [
            'block' => 'required|integer',
            'exchange' => 'required|integer',
            'pointShare' => 'required|integer',
        ];
    }

    public function messages() {
        return [
            'block.required' => 'Bạn phải nhập nhận điểm thưởng',
            'block.integer' => 'Giá trị nhận điểm thưởng phải là dạng số',
            'exchange.required' => 'Bạn phải nhập giá trị quy đổi',
            'exchange.integer' => 'Giá trị quy đổi điểm thưởng phải là dạng số',            
			'pointShare.required' => 'Bạn phải nhập điểm thưởng khi người dùng chia sẻ ',
            'pointShare.integer' => 'Điểm thưởng khi người dùng chia sẻ phải là dạng số',
        ];
    }
}
