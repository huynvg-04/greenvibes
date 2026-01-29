<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Category;
use App\Http\Requests\StoreBlogRequest;
use App\Http\Requests\UpdateBlogRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Blog::class);

        $categories = Category::where('type', 'blog')->get();

        $stats = [
            'total'     => Blog::count(),
            'published' => Blog::where('is_published', true)->count(),
            'draft'     => Blog::where('is_published', false)->count(),
            'views'     => Blog::sum('views')
        ];

        // Khởi tạo query ban đầu
        $query = Blog::with(['user', 'category']);

        // 1. LỌC THEO TỪ KHÓA
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('excerpt', 'like', "%{$keyword}%");
            });
        }

        // 2. LỌC THEO TRẠNG THÁI
        if ($request->has('is_published') && $request->is_published !== null && $request->is_published !== '') {
            $query->where('is_published', $request->is_published);
        }

        // 3. [MỚI] SẮP XẾP (SORT)
        // Mặc định là 'latest' (Mới nhất)
        $sort = $request->input('sort', 'latest');

        switch ($sort) {
            case 'views_desc':
                $query->orderBy('views', 'desc'); // Xem nhiều nhất
                break;
            case 'views_asc':
                $query->orderBy('views', 'asc');  // Xem ít nhất
                break;
            case 'oldest':
                $query->oldest(); // Cũ nhất
                break;
            default:
                $query->latest(); // Mới nhất (mặc định)
                break;
        }

        // 4. PHÂN TRANG
        $perPage = $request->input('per_page', 10);
        $allowedPerPage = [10, 20, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        $blogs = $query->paginate($perPage)->appends($request->all());

        return view('admin.blogs.index', compact('blogs', 'stats', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', Blog::class);

        $categories = Category::where('type', 'blog')->get();

        return view('admin.blogs.create', compact('categories'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\StoreBlogRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Blog::class);

        $request->validate([
            'title'       => 'required|string|max:255',
            'slug'        => 'required|string|max:255|unique:blogs,slug',
            'category_id' => 'required|exists:categories,id',
            'content'     => 'required',
            'thumbnail'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'title.required' => 'Tiêu đề không được để trống.',
            'title.string' => 'Tiêu đề không hợp lệ',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'slug.required' => 'Slug không được để trống.',
            'slug.unique' => 'Slug đã tồn tại.',
            'category_id.required' => 'Danh mục không được để trống.',
            'category_id.exists' => 'Danh mục không tồn tại.',
            'content.required' => 'Nội dung không được để trống.',
            'thumbnail.image' => 'Ảnh không hợp lệ.',
            'thumbnail.mimes' => 'Ảnh không hợp lệ.',
            'thumbnail.max' => 'Ảnh không được vượt quá 2048 ký tự.',

        ]);

        $data = $request->all();

        if ($request->filled('slug')) {
            $data['slug'] = Str::slug($request->slug);
        } else {
            $data['slug'] = Str::slug($request->title);
        }

        $data['user_id'] = Auth::id();

        $data['is_published'] = $request->has('is_published');

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('blogs', 'public');
            $data['thumbnail'] = $path;
        }

        Blog::create($data);

        return redirect()->route('admin.blogs.index')->with('success', 'Tạo bài viết thành công!');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Blog $blog)
    {
        $this->authorize('view', $blog);
        return view('admin.blogs.show', compact('blog'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Blog $blog)
    {
        $this->authorize('update', $blog);

        $categories = Category::where('type', 'blog')->get();

        return view('admin.blogs.edit', compact('blog', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\UpdateBlogRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Blog $blog)
    {
        $this->authorize('update', $blog);

        $request->validate([
            'title'       => 'required|string|max:255',
            'slug'        => 'required|string|unique:blogs,slug,' . $blog->id,
            'category_id' => 'nullable|exists:categories,id',
            'content'     => 'required',
            'thumbnail'   => 'nullable|image|max:2048',
        ], [
            'title.required' => 'Tiêu đề không được để trống.',
            'title.string' => 'Tiêu đề không hợp lệ',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'slug.required' => 'Slug không được để trống.',
            'slug.unique' => 'Slug đã tồn tại.',
            'category_id.required' => 'Danh mục không được để trống.',
            'category_id.exists' => 'Danh mục không tồn tại.',
            'content.required' => 'Nội dung không được để trống.',
            'thumbnail.image' => 'Ảnh không hợp lệ.',
            'thumbnail.mimes' => 'Ảnh không hợp lệ.',
            'thumbnail.max' => 'Ảnh không được vượt quá 2048 ký tự.',
        ]);

        $data = $request->all();

        $data['slug'] = Str::slug($request->slug);

        $data['is_published'] = $request->has('is_published');

        if ($request->hasFile('thumbnail')) {
            if ($blog->thumbnail) {
                Storage::disk('public')->delete($blog->thumbnail);
            }
            $path = $request->file('thumbnail')->store('blogs', 'public');
            $data['thumbnail'] = $path;
        }

        $blog->update($data);

        return redirect()->route('admin.blogs.index')->with('success', 'Cập nhật bài viết.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Blog $blog)
    {
        $this->authorize('delete', $blog);

        if ($blog->thumbnail) {
            Storage::disk('public')->delete($blog->thumbnail);
        }

        $blog->delete();

        return redirect()->route('admin.blogs.index')->with('success', 'Đã xóa bài viết.');
    }
}
