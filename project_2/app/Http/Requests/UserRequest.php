<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class UserRequest extends Request {

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
            'fullname' => 'required|min:4',
            'email' => 'email',
            'price' => 'required|integer',
            'mobile' => 'required',//|integer',
            'count_product' => 'integer',
            'profit' => 'required|integer',
        ];
    }

    public function messages() {
        return [
            'fullname.required' => 'Bạn phải nhập họ tên khách hàng',
            'fullname.min' => 'Họ tên khách hàng lớn hơn 4 ký tự',
            //'email.required' => 'Bạn phải nhập email khách hàng',
            'email.email' => 'Bạn phải nhập đúng định dạng email',
            'price.required' => 'Bạn phải nhập giá trị đơn hàng',
            'price.integer' => 'Bạn phải nhập đúng định dạng hóa trị đơn hàng',
            //'mobile.integer' => 'Số điện thoại phải dạng số',
            'mobile.required' => 'Bạn phải nhập số điện thoại',
            'count_product.integer' => 'Số sản phẩm/số lần sử dụng phải dạng số',
            'profit.required' => 'Bạn phải nhập lợi nhuận',
            'cmt.integer' => 'Lợi nhuận là dạng số',
        ];
    }

}
