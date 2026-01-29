@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="mb-4">Chi tiết khách hàng</h1>

    <div class="card">
        <div class="card-body">
            <p><strong>ID:</strong> {{ $user->id }}</p>
            <p><strong>Tên:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Vai trò:</strong> {{ ucfirst($user->role) }}</p>
            <p><strong>Ngày tạo:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary mt-3">Quay lại</a>
</div>
@endsection
