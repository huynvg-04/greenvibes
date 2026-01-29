<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductImageRequest extends FormRequest
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
        if ($this->has('ids')) {
            return [
                'ids'   => 'required|array',
                'ids.*' => 'exists:product_images,id',
            ];
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return [
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:51200',
                'is_primary' => 'nullable|boolean',
            ];
        }

        return [
            'images'   => 'required|array', 
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:51200', 
            'is_primary' => 'nullable|boolean',
        ];
    }

    public function attributes()
    {
        return [
            'ids' => 'Danh sách ảnh',
            'image' => 'File ảnh',
            'images' => 'Danh sách file ảnh', 
            'images.*' => 'File ảnh',       
            'is_primary' => 'Trạng thái ảnh chính',
        ];
    }

    public function messages()
    {
        return [
            'ids.required' => 'Vui lòng chọn ít nhất một ảnh để xóa.',
            'ids.array'    => 'Dữ liệu chọn không hợp lệ.',
            'ids.*.exists' => 'Ảnh đã chọn không tồn tại.',

            'image.image' => 'File tải lên phải là hình ảnh.',
            'image.mimes' => 'Định dạng ảnh không hợp lệ (chỉ chấp nhận .jpg, .png, .gif, .webp).',
            'image.max'   => 'Dung lượng ảnh quá lớn (Tối đa 50MB).',

            'images.required' => 'Vui lòng chọn ít nhất một ảnh.',
            'images.array'    => 'Dữ liệu ảnh không hợp lệ.',
            
            'images.*.required' => 'File ảnh bị lỗi hoặc không tồn tại.',
            'images.*.image'    => 'Một trong các file tải lên không phải là ảnh.',
            'images.*.mimes'    => 'Định dạng ảnh không hợp lệ (chỉ chấp nhận .jpg, .png, .gif, .webp).',
            'images.*.max'      => 'Một trong các ảnh có dung lượng quá lớn (Tối đa 50MB).',
        ];
    }
}