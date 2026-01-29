<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductVariantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'sku' => [
                'required',
                'unique:product_variants,sku',
                'regex:/^[a-zA-Z0-9-_]+$/'
            ],
            'standard_cost' => 'required|numeric|min:1',
            'list_price'    => 'required|numeric|gte:standard_cost',
            'attributes' => [
                'array',
                function ($attribute, $value, $fail) {
                    $filtered = array_filter($value, function ($item) {
                        return !is_null($item) && $item !== '';
                    });

                    if (empty($filtered)) {
                        $fail('Vui lòng chọn ít nhất một thuộc tính.');
                    }
                },
            ],

            'attributes.*' => 'nullable|integer|exists:attribute_values,id',
        ];
    }

    public function messages(): array
    {
        return [
            'sku.required'           => 'Vui lòng nhập mã SKU.',
            'sku.regex'    => 'SKU không được chứa dấu, khoảng trắng hoặc ký tự đặc biệt (chỉ dùng chữ không dấu, số, - và _).',
            'sku.unique'             => 'Mã SKU này đã tồn tại trong hệ thống.',

            'standard_cost.required' => 'Vui lòng nhập giá nhập.',
            'standard_cost.numeric'  => 'Giá nhập phải là số.',
            'standard_cost.min'      => 'Giá nhập phải lớn hơn 0.',

            'list_price.required'    => 'Vui lòng nhập giá niêm yết.',
            'list_price.numeric'     => 'Giá niêm yết phải là số.',
            'list_price.gte'         => 'Giá niêm yết phải lớn hơn hoặc bằng giá nhập.',

            'attributes.required'    => 'Vui lòng chọn ít nhất một thuộc tính.',
            'attributes.array'       => 'Định dạng thuộc tính không hợp lệ.',
        ];
    }
}
