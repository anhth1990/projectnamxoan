<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ConfigRatingRequest extends Request {

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
            'type' => 'required',
            //'start_date' => 'required|date|after:today',
            //'finish_date' => 'required|date|after:start_date',
            //'block' => 'required|integer',
            //'exchange' => 'required|integer',
        ];
    }

    public function messages() {
        return [
            'type.required' => 'Bạn phải chọn kiểu xếp hạng',
            /*
            'start_date.required' => 'Bạn phải nhập ngày bắt đầu',
            'start_date.date' => 'Ngày bắt đầu chiến dịch phải là định dạng ngày',
            'start_date.after' => 'Ngày bắt đầu phải tính từ ngày mai',
            'finish_date.required' => 'Bạn phải nhập ngày kết thúc',
            'finish_date.date' => 'Ngày bắt đầu chiến dịch phải là định dạng ngày',
            'finish_date.after' => 'Ngày kết thúc phải sau ngày bắt đầu',        
            'block.required' => 'Bạn phải nhập nhận điểm thưởng',
            'block.integer' => 'Giá trị nhận điểm thưởng phải là dạng số',
            'exchange.required' => 'Bạn phải nhập giá trị quy đổi',
            'exchange.integer' => 'Giá trị quy đổi điểm thưởng phải là dạng số',
             *              */
        ];
    }

}
