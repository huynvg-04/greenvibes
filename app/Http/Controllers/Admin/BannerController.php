<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use App\Services\ImageUploadService;

class BannerController extends Controller
{
    public function __construct(protected ImageUploadService $imageService) {}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Banner::class);

        $query = Banner::query()->latest('id');

        if ($request->filled('keyword')) {
            $query->where('title', 'like', '%' . $request->keyword . '%');
        }

        if ($request->has('status') && $request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }


        $perPage = $request->input('per_page', 10);

        $allowedPerPage = [10, 20, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        $banners = $query->paginate($perPage)->appends($request->all());

        return view('admin.banners.index', compact('banners'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', Banner::class);
        return view('admin.banners.create');
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Banner::class);

        $request->validate([
            'title' => 'required|string',
            'image' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:10240',
            'link' => 'nullable|url',
            'status' => 'required|boolean',
        ]);

        // Upload banner (webp 1920px)
        $imagePath = $this->imageService->upload(
            $request->file('image'),
            'banners',
            1920,
            100,
            'webp',
            'banner_'
        );

        Banner::create([
            'title' => $request->title,
            'image' => $imagePath,
            'link' => $request->link,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Thêm mới banner.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Banner  $banner
     * @return \Illuminate\Http\Response
     */
    public function edit(Banner $banner)
    {
        $this->authorize('update', $banner);
        return view('admin.banners.edit', compact('banner'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Banner  $banner
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Banner $banner)
    {
        $this->authorize('update', $banner);

        $request->validate([
            'title' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:10240',
            'link' => 'nullable|url',
            'status' => 'required|boolean',
        ]);

        $data = [
            'title' => $request->title,
            'link' => $request->link,
            'status' => $request->status,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $this->imageService->replace(
                $request->file('image'),
                $banner->image,
                'banners',
                1920,
                100,
                'webp',
                'banner_'
            );
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Cập nhật banner.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Banner  $banner
     * @return \Illuminate\Http\Response
     */
    public function destroy(Banner $banner)
    {
        $this->authorize('delete', $banner);

        $this->imageService->delete($banner->image);

        $banner->delete();
        return redirect()->route('admin.banners.index')->with('success', 'Xóa banner.');
    }

}
