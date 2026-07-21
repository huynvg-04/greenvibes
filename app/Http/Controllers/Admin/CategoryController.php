<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\ImageUploadService;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function __construct(protected ImageUploadService $imageService)
    {
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Category::class);

        $query = Category::query();

        if ($request->keyword) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }


        $perPage = $request->input('per_page', 10);

        $allowedPerPage = [10, 20, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        $categories = $query->paginate($perPage)->appends($request->all());

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', Category::class);
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Category::class);

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug',
            'type' => 'required|in:product,blog',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ], [
            'name.unique' => 'Tên danh mục này đã tồn tại, vui lòng chọn tên khác.',
            'name.required' => 'Tên danh mục không được để trống.',
            'name.string' => 'Tên danh mục không hợp lệ.',
            'name.max' => 'Tên danh mục không được quá 255 ký tự.',
            'slug.required' => 'Slug không được để trống.',
            'slug.string' => 'Tên đường dẫn không hợp lệ.',
            'slug.max' => 'Slug không được quá 255 ký tự.',
            'slug.unique' => 'Slug (đường dẫn) này đã tồn tại, vui lòng chọn tên khác.',
            'type.in' => 'Loại danh mục không hợp lệ.'
        ]);

        $data = [
            'name' => $request->name,
            'slug' => $request->slug,
            'type' => $request->type,
            'image' => null,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $this->imageService->upload($request->file('image'), 'categories');
        }

        Category::create($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Thêm mới danh mục.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        $this->authorize('view', $category);
        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        $this->authorize('update', $category);
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $this->authorize('update', $category);

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories')->ignore($category->id)
            ],
            'type' => 'required|in:product,blog',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ]);

        $data = $request->only(['name', 'slug', 'type']);


        if ($request->hasFile('image')) {
            $data['image'] = $this->imageService->replace($request->file('image'), $category->image, 'categories');
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Cập nhật danh mục.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);

        $this->imageService->delete($category->image);

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Xóa danh mục.');
    }
}
