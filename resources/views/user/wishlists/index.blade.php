@extends('layouts.app')
@section('title','Danh sách yêu thích')

@section('content')
<div class="wishlist-container">
    <div class="wishlist-header">
        <h1 class="wishlist-title">Danh sách yêu thích</h1>
    </div>


    @if($wishlists->isEmpty())
    <p>Bạn chưa có sản phẩm yêu thích nào.</p>
    @else
    @foreach ($wishlists as $item)
    <div style="border-bottom:1px solid #ccc; padding:10px 0;">
        <strong>{{ $item->product->name }}</strong>
        <form action="{{ route('user.wishlists.destroy', $item->product->id) }}" method="POST" style="display:inline">
            @csrf
            @method('DELETE')
            <button type="submit">Bỏ thích</button>
        </form>
    </div>
    @endforeach
    @endif
</div>

<style>
    .wishlist-header {
        height: 140px;
        background: #f4f4f4;
        margin-top: 50px;
        padding: var(--space-xl) 0 var(--space-xl) 60px;
        font-family: var(--font-body);
        display: flex;
        align-items: center;
    }

    .wishlist-title {
        font-family: var(--font-ui);
        font-size: var(--type-h2);
        font-weight: 300;
        color: var(--color-primary);
        margin: 0;
        letter-spacing: 0.5px;
    }
</style>
@endsection