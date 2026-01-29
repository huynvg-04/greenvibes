<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $product = $this->route('product');
        $productId = $product ? (is_object($product) ? $product->id : $product) : null;

        return [
            'sku' => ['required', 'string', 'max:255', $productId ? "unique:products,sku,{$productId}" : 'unique:products,sku'],
            'name' => ['required', 'string', 'max:255', $productId ? "unique:products,name,{$productId}" : 'unique:products,name'],
            'slug' => ['required', 'string', 'max:255', $productId ? "unique:products,slug,{$productId}" : 'unique:products,slug'],
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'status' => ['sometimes', 'boolean'],
            'tags' => ['nullable'],

            // --- THÊM DÒNG NÀY ---
            // min:0 nghĩa là >= 0. Nếu bạn bắt buộc phải LỚN HƠN 0 hẳn (không lấy số 0) thì dùng gt:0
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function messages()
    {
        return [
            'sku.required' => 'SKU không được để trống.',
            'sku.unique' => 'SKU đã tồn tại.',
            'name.required' => 'Tên sản phẩm không được để trống.',
            'name.unique' => 'Tên sản phẩm đã tồn tại.',
            'slug.required' => 'Slug không được để trống.',
            'slug.unique' => 'Slug đã được sử dụng.',
            'category_id.required' => 'Danh mục không được để trống.',
            'category_id.exists' => 'Danh mục không tồn tại.',

            'discount_percent.numeric' => 'Giảm giá phải là dạng số.',
            'discount_percent.min' => 'Giảm giá không được nhỏ hơn 0.',
            'discount_percent.max' => 'Giảm giá không được vượt quá 100%.',
        ];
    }
}
