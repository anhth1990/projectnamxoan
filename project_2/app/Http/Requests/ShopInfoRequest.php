<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ShopInfoRequest extends Request
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
            'sort_name' => 'max:100',
            'full_name' => 'required|max:250',
            //'boss_name' => 'required|max:100',
            //'email' => 'email|max:100',
            //'image' => 'required|image',
            'address' => 'required',
            'introduct' => 'required',
            //'mobile' => 'required|integer|min:10|max:11',
        ];
    }
    public function messages() {
        return [
            'sort_name.max' => 'Tên ngắn gọn phải dưới 100 ký tự',
            'full_name.integer' => 'Tên đầy đủ không được quá 250 ký tự',
            'full_name.required' => 'Bạn phải nhập tên đầy đủ của shop',
            'boss_name.max' => 'Tên chủ doanh nghiệp không quá 100 ký tự',
            'boss_name.required' => 'Bạn phải nhập tên chủ doanh nghiệp',
            'email.email' => 'Email chưa đúng định dạng',
            'email.max' => 'Email không quá 100 ký tự',
            //'image.required' => 'Bạn phải chọn ảnh đại diện',
            //'image.image' => 'Ảnh đại diện chưa đúng định dạng',
            'introduct.required' => 'Bạn phải nhập phần giới thiệu của hàng',
            /*'mobile.required' => 'Bạn phải nhập số điện thoại',
            'mobile.integer' => 'Số điện thoại chỉ bao gồm chữ số',
            'mobile.max' => 'Số điện thoại không quá 11 số',
            'mobile.min' => 'Số điện thoại không nhỏ hơn 10 số',
            */'address.required' => 'Bạn phải nhập địa chỉ cửa hàng',
        ];
    }
}
