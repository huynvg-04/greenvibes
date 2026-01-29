<?php

namespace App\Http\Controllers;

use App\Models\Example; // Nhớ use Model
use Illuminate\Http\Request;

class ExampleController extends Controller
{
    /**
     * 1. INDEX: Xem danh sách
     * -> Chưa chọn cái nào cụ thể -> Dùng Class
     */
    public function index()
    {
        $this->authorize('viewAny', Example::class);

        // Code lấy danh sách...
    }

    /**
     * 2. CREATE: Hiện form thêm mới
     * -> Chưa tạo ra cái nào -> Dùng Class
     */
    public function create()
    {
        $this->authorize('create', Example::class);

        // Code return view...
    }

    /**
     * 3. STORE: Lưu cái mới
     * -> Đang lưu, chưa có ID -> Dùng Class
     */
    public function store(Request $request)
    {
        $this->authorize('create', Example::class);

        // Code validate và create...
    }

    /**
     * 4. SHOW: Xem chi tiết 1 cái
     * -> Đã có đối tượng cụ thể ($example) -> Dùng Biến $example
     */
    public function show(Example $example)
    {
        $this->authorize('view', $example);

        // Code return view...
    }

    /**
     * 5. EDIT: Sửa 1 cái
     * -> Đã biết sửa cái nào ($example) -> Dùng Biến $example
     */
    public function edit(Example $example)
    {
        $this->authorize('update', $example); 

        // Code return view...
    }

    /**
     * 6. UPDATE: Lưu cập nhật
     * -> Đã biết update cái nào ($example) -> Dùng Biến $example
     */
    public function update(Request $request, Example $example)
    {
        $this->authorize('update', $example);

        // Code update...
    }

    /**
     * 7. DESTROY: Xóa 1 cái
     * -> Đã biết xóa cái nào ($example) -> Dùng Biến $example
     */
    public function destroy(Example $example)
    {
        $this->authorize('delete', $example); // Policy thường đặt tên là delete (hoặc forceDelete)

        // Code delete...
    }
}