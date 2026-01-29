<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Attribute::class);

        $query = Attribute::with('values')->latest();

        if ($request->has('keyword') && $request->keyword != '') {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('slug', 'like', "%{$keyword}%");
            });
        }

        $perPage = $request->input('per_page', 10);

        $allowedPerPage = [10, 20, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }
        
        $attributes = $query->paginate($perPage)->appends($request->all());

        return view('admin.attributes.index', compact('attributes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', Attribute::class);
        return view('admin.attributes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Attribute::class);

        $request->validate([
            'name' => 'required|string|unique:attributes,name|max:255',
        ], [
            'name.required' => 'Tên thuộc tính không được để trống.',
            'name.unique' => 'Tên thuộc tính này đã tồn tại.',
        ]);

        Attribute::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return back()->with('success', 'Thêm mới thuộc tính.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Attribute $attribute)
    {
        $this->authorize('update', $attribute);
        $values = $attribute->values()->latest()->get();
        return view('admin.attributes.edit', compact('attribute', 'values'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Attribute $attribute)
    {
        $this->authorize('update', $attribute);
        $request->validate([
            'name' => 'required|string|unique:attributes,name,' . $attribute->id . '|max:255',
        ]);

        $attribute->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('admin.attributes.index')->with('success', 'Cập nhật thuộc tính.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attribute $attribute)
    {
        $this->authorize('delete', $attribute);

        $attribute->delete();
        return redirect()->route('admin.attributes.index')->with('success', 'Xóa thuộc tính.');
    }

    public function storeValue(Request $request, Attribute $attribute)
    {
        $this->authorize('create', $attribute);

        $request->validate([
            'value' => 'required|string|max:255',
            'code'  => 'nullable|string|max:50',
        ]);

        $attribute->values()->create([
            'value' => $request->value,
            'code'  => $request->code,
        ]);

        return back()->with('success', 'Thêm giá trị mới.');
    }

    public function destroyValue(AttributeValue $attributeValue)
    {
        $this->authorize('delete', $attributeValue);

        $attributeValue->delete();
        return back()->with('success', 'Xóa giá trị.');
    }
}
