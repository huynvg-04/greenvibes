{{-- THÊM THẺ BAO NGOÀI CÙNG --}}
<div class="modal fade" id="createBlogModal" tabindex="-1" aria-hidden="true" 
     data-has-errors="{{ $errors->any() && old('_method') != 'PUT' ? 'true' : 'false' }}">
     
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light rounded-top-4">
                <div class="d-flex align-items-center">
                    <h5 class="modal-title fw-bold text-body mb-0">Viết bài mới</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
    
            <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-0">
                    <ul class="nav nav-tabs border-bottom" id="createBlogTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link py-2 small fw-semibold text-uppercase rounded-0 hover-text-body border-bottom-0 active" id="create-info-tab" data-bs-toggle="tab" data-bs-target="#create-info-pane" type="button" role="tab">Thông tin chung</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link py-2 small fw-semibold text-uppercase rounded-0 hover-text-body border-bottom-0" id="create-content-tab" data-bs-toggle="tab" data-bs-target="#create-content-pane" type="button" role="tab">Nội dung chi tiết</button>
                        </li>
                    </ul>
    
                    <div class="tab-content p-4">
                        <div class="tab-pane fade show active" id="create-info-pane" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-lg-8 border-end">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold required">Tiêu đề</label>
                                        <input type="text" name="title" id="create_title" class="form-control" 
                                               placeholder="Nhập tiêu đề..." value="{{ old('title') }}" 
                                               onkeyup="generateSlug('create_title', 'create_slug')">
                                    </div>
    
                                    <div class="mb-3">
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="slug" id="create_slug" class="form-control bg-light text-body border-start-0" 
                                                   value="{{ old('slug') }}" readonly>
                                        </div>
                                    </div>
    
                                    <div class="mb-0">
                                        <label class="form-label fw-bold small text-uppercase text-body">Mô tả ngắn (Excerpt)</label>
                                        <textarea name="excerpt" class="form-control" rows="4">{{ old('excerpt') }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Danh mục</label>
                                        <select name="category_id" class="form-select form-control">
                                            <option value="">-- Chọn --</option>
                                            @foreach($categories as $category)
                                                @if($category->type == 'blog')
                                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
    
                                <div class="col-lg-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Trạng thái</label>
                                        <div class="p-3 bg-light rounded-3 border border-dashed d-flex justify-content-between align-items-center">
                                            <span id="create_publish_label" class="small fw-bold text-uppercase {{ old('is_published', '1') ? 'text-success' : 'text-muted' }}">
                                                {{ old('is_published', '1') ? 'Công khai' : 'Bản nháp' }}
                                            </span>
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input cursor-pointer switch-success" type="checkbox" name="is_published" id="create_is_published" value="1" {{ old('is_published', '1') ? 'checked' : '' }} onchange="toggleBlogStatus(this, 'create_publish_label')">
                                            </div>
                                        </div>
                                    </div>
    
                                    <div class="mb-0">
                                        <label class="form-label fw-bold small text-uppercase text-body">Ảnh đại diện</label>
                                        <div class="card border border-dashed bg-light text-center">
                                            <div class="card-body p-2">
                                                <div class="mb-2 position-relative" style="height: 140px; background: #fff;">
                                                    <img id="create_preview_img" src="" class="img-fluid h-100 object-fit-contain d-none">
                                                    <div id="create_placeholder_text" class="placeholder-text d-flex align-items-center justify-content-center h-100 text-body small">
                                                        <div><i class='bx bx-image fs-1 d-block mb-1'></i>Chưa chọn ảnh</div>
                                                    </div>
                                                </div>
                                                <input type="file" name="thumbnail" class="form-control form-control-sm" onchange="previewImage(this, 'create_preview_img')">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
    
                        <div class="tab-pane fade" id="create-content-pane" role="tabpanel" style="min-height: 357px">
                            <textarea name="content" id="create_content_editor" class="form-control" rows="20">{{ old('content') }}</textarea>
                        </div>
                    </div>
                </div>
    
                <div class="modal-footer border-top-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light d-flex align-items-center gap-2 px-4 py-2" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2">Thêm mới</button>
                </div>
            </form>
        </div>
    </div>
</div>