<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\Category;
use Illuminate\Support\Facades\Session; 
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
        $query = Blog::where('is_published', true);

        if ($request->has('keyword')) {
            $query->where('title', 'like', '%' . $request->keyword . '%');
        }

        if ($request->has('category')) {
            $slug = $request->category;
            $query->whereHas('category', function($q) use ($slug) {
                $q->where('slug', $slug);
            });
        }

        $blogs = $query->with(['user', 'category'])
            ->withCount('likes')
            ->latest()
            ->paginate(5);

        $recentPosts = Blog::where('is_published', true)
            ->latest()
            ->take(4)
            ->get();

        $categories = Category::where('type', 'blog')
            ->withCount(['blogs' => function($q){
                $q->where('is_published', true);
            }])
            ->get();

        return view('user.blogs.index', compact('blogs', 'recentPosts', 'categories'));
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $blog = Blog::where('slug', $slug)
            ->where('is_published', true)
            ->withCount('likes') 
            ->firstOrFail();

        $blogKey = 'blog_viewed_' . $blog->id;
        if (!Session::has($blogKey)) {
            $blog->increment('views');
            Session::put($blogKey, 1);
        }

        $relatedBlogs = Blog::where('category_id', $blog->category_id)
            ->where('id', '!=', $blog->id)
            ->where('is_published', true)
            ->take(3)
            ->get();

        $recentPosts = Blog::where('is_published', true)->latest()->take(4)->get();
        $categories = Category::where('type', 'blog')->withCount('blogs')->get();

        return view('user.blogs.show', compact('blog', 'relatedBlogs', 'recentPosts', 'categories'));
    }


    public function like($id)
    {
        if (!Auth::check()) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $blog = Blog::findOrFail($id);
 
        $blog->likes()->toggle(Auth::id());

        return response()->json([
            'status' => 'success',
            'liked' => $blog->isLikedByAuthUser(), 
            'count' => $blog->likes()->count()   
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

