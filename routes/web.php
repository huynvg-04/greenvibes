<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ChatbotController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\CustomerController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\AiController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\Admin\ProductImageController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\ShippingRateController;
use App\Http\Controllers\Admin\ReturnController;
use App\Http\Controllers\Admin\MembershipTierController;


use App\Http\Controllers\ReviewController;



Route::get('/test', function () {
    return view('welcome');
});

Auth::routes(['reset' => false, 'verify' => true, 'register' => false]);  //update

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function ($id, $hash) {
    $user = User::findOrFail($id);

    if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        abort(403, 'Link xác thực không hợp lệ');
    }

    if ($user->hasVerifiedEmail()) {
        return redirect('/')->with('success', 'Email đã được xác thực trước đó.');
    }

    $user->markEmailAsVerified();
    event(new Verified($user));

    Auth::login($user);
    $profile = $user->customerProfile;
    if (!$profile || empty($profile->phone) || empty($profile->address)) {
        return redirect()->route('user.profile.edit')
            ->with('success', 'Xác thực email thành công! Vui lòng cập nhật thông tin cá nhân.');
    }

    return redirect('/')->with('success', 'Xác thực email thành công!');
})->middleware(['signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Đã gửi lại link xác thực!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('quen-mat-khau', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::post('/chatbot/send', [ChatbotController::class, 'chat'])->name('chatbot.send');
Route::get('/chatbot/history', [ChatbotController::class, 'history'])->name('chatbot.history');
Route::get('/', [ProductController::class, 'home'])->name('home');



Route::get('/dang-nhap', [LoginController::class, 'showCustomerLoginForm'])->name('login');
Route::post('/dang-nhap', [LoginController::class, 'customerLogin']);
Route::get('/dang-ky', [RegisterController::class, 'showRegistrationForm'])
    ->name('register');

Route::post('/dang-ky', [RegisterController::class, 'register']);


Route::get('dang-nhap/facebook', [LoginController::class, 'redirectToFacebook']);
Route::get('login/facebook/callback', [LoginController::class, 'handleFacebookCallback']);
Route::get('dang-nhap/google', [LoginController::class, 'redirectToGoogle']);
Route::get('login/google/callback', [LoginController::class, 'handleGoogleCallback']);


Route::get('/admin/login', [LoginController::class, 'showAdminLoginForm'])->name('admin.login')->middleware('guest');
Route::post('/admin/login', [LoginController::class, 'adminLogin']);


Route::get('/san-pham', [ProductController::class, 'index'])->name('products.index');
Route::get('/san-pham/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('/search', [ProductController::class, 'search'])->name('products.search');
Route::get('/search-suggestions', [ProductController::class, 'suggestions'])->name('products.suggestions');
Route::get('/bai-viet', [BlogController::class, 'index'])->name('user.blogs.index');
Route::get('/bai-viet/{slug}', [BlogController::class, 'show'])->name('user.blogs.show');
Route::post('/blog/like/{id}', [BlogController::class, 'like'])->name('blogs.like');
Route::post('/reviews/{review}/like', [App\Http\Controllers\ReviewController::class, 'toggleLike'])->name('reviews.like');

Route::middleware(['auth', 'role:customer'])->group(function () {

    Route::prefix('gio-hang')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('user.cart.index');
        Route::post('/add', [CartController::class, 'add'])->name('user.cart.add');
        Route::post('/update', [CartController::class, 'update'])->name('user.cart.update');
        Route::post('/remove', [CartController::class, 'remove'])->name('user.cart.remove');

        Route::post('/select', [CartController::class, 'updateSelection'])->name('user.cart.select');
    });

    Route::get('/thanh-toan', [CheckoutController::class, 'index'])->name('user.checkout.index');

    Route::post('/thanh-toan/process', [CheckoutController::class, 'process'])->name('user.checkout.process');

    Route::post('/checkout/apply-coupon', [CheckoutController::class, 'applyCoupon'])->name('user.checkout.apply_coupon');
    Route::delete('/checkout/remove-coupon', [CheckoutController::class, 'removeCoupon'])->name('user.checkout.remove_coupon');

    Route::get('/dat-hang-thanh-cong/{code}', [CheckoutController::class, 'success'])->name('user.checkout.success');
    Route::get('/payment/create/{orderId}', [PaymentController::class, 'createPayment'])->name('payment.create');

    Route::get('/payment/return', [PaymentController::class, 'paymentReturn'])->name('payment.return');
    Route::get('/payment/momo/create/{orderId}', [PaymentController::class, 'createMomoPayment'])->name('payment.momo.create');
    Route::get('/payment/momo/return', [PaymentController::class, 'momoReturn'])->name('payment.momo.return');
    Route::patch('/don-hang/{order}/huy', [OrderController::class, 'cancel'])->name('user.orders.cancel');
    Route::patch('/don-hang/{order}/hoan-thanh', [OrderController::class, 'complete'])->name('user.orders.complete');
    Route::get('/don-hang/{order}/hoan-hang', [OrderController::class, 'returnForm'])->name('user.orders.return');
    Route::post('/don-hang/{order}/hoan-hang', [OrderController::class, 'storeReturn'])->name('user.orders.return.store');
    Route::post('/reviews/{itemId}', [ReviewController::class, 'store'])->name('reviews.store');
});



Route::middleware('auth')->group(function () {
    Route::get('/ho-so-ca-nhan', [App\Http\Controllers\ProfileController::class, 'edit'])->name('user.profile.edit');
    Route::put('/ho-so-ca-nhan', [App\Http\Controllers\ProfileController::class, 'update'])->name('user.profile.update');
    Route::get('/don-hang', [OrderController::class, 'index'])->name('user.orders.index');
    Route::get('/don-hang/tao-moi', [OrderController::class, 'create'])->name('user.orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('user.orders.store');
    Route::post('/orders/item/{item}/review', [OrderController::class, 'storeItemReview'])
        ->name('user.orders.item.review');
    Route::get('/yeu-thich', [WishlistController::class, 'index'])->name('user.wishlists.index');
    Route::post('/wishlists/create/{id}', [WishlistController::class, 'create'])->name('user.wishlists.create');
    Route::delete('/wishlists/destroy/{id}', [WishlistController::class, 'destroy'])->name('user.wishlists.destroy');
    Route::post('/wishlist/toggle/{product}', [WishlistController::class, 'toggle'])->name('user.wishlists.toggle');
    Route::get('/thong-bao/doc/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::get('/thong-bao/doc-tat-ca', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
});


Route::prefix('admin')->middleware(['auth', 'role:manager|staff', 'prevent-back-history'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::resource('coupons', CouponController::class)->names([
        'index' => 'admin.coupons.index',
        'create' => 'admin.coupons.create',
        'store' => 'admin.coupons.store',
        'edit' => 'admin.coupons.edit',
        'update' => 'admin.coupons.update',
        'destroy' => 'admin.coupons.destroy',
    ]);


    Route::resource('customers', CustomerController::class)->names([
        'index' => 'admin.customers.index',
        'create' => 'admin.customers.create',
        'store' => 'admin.customers.store',
        'edit' => 'admin.customers.edit',
        'update' => 'admin.customers.update',
        'destroy' => 'admin.customers.destroy',
    ]);


    Route::resource('categories', CategoryController::class)->names([
        'index' => 'admin.categories.index',
        'create' => 'admin.categories.create',
        'store' => 'admin.categories.store',
        'show' => 'admin.categories.show',
        'edit' => 'admin.categories.edit',
        'update' => 'admin.categories.update',
        'destroy' => 'admin.categories.destroy',
    ]);


    Route::resource('products', AdminProductController::class)->names([
        'index' => 'admin.products.index',
        'create' => 'admin.products.create',
        'store' => 'admin.products.store',
        'show' => 'admin.products.show',
        'edit' => 'admin.products.edit',
        'update' => 'admin.products.update',
        'destroy' => 'admin.products.destroy',
    ]);


    Route::resource('banners', BannerController::class)->names([
        'index' => 'admin.banners.index',
        'create' => 'admin.banners.create',
        'store' => 'admin.banners.store',
        'show' => 'admin.banners.show',
        'edit' => 'admin.banners.edit',
        'update' => 'admin.banners.update',
        'destroy' => 'admin.banners.destroy',
    ]);

    Route::controller(ProductImageController::class)
        ->prefix('product_images')
        ->name('admin.product_images.')
        ->group(function () {

            Route::get('/', 'index')->name('index');

            Route::get('{product}', 'show')->name('show');
            Route::get('create/{product}', 'create')->name('create');
            Route::post('store/{product}', 'store')->name('store');

            Route::post('products/{product}/delete-selected', 'destroySelected')->name('destroySelected');
            Route::delete('products/{product}/delete-all', 'destroyAll')->name('destroyAll');

            Route::get('edit/{product}/{productImage}', 'edit')->name('edit');
            Route::put('update/{product}/{productImage}', 'update')->name('update');
            Route::delete('destroy/{product}/{productImage}', 'destroy')->name('destroy');
        });

    Route::controller(ProductVariantController::class)->group(function () {
        Route::get('products/{product}/variants/create', 'create')->name('admin.product_variants.create');
        Route::post('products/{product}/variants', 'store')->name('admin.product_variants.store');
    });

    Route::resource('product_variants', ProductVariantController::class)
        ->except(['create', 'store'])
        ->names([
            'index' => 'admin.product_variants.index',
            'show' => 'admin.product_variants.show',
            'edit' => 'admin.product_variants.edit',
            'update' => 'admin.product_variants.update',
            'destroy' => 'admin.product_variants.destroy',
        ]);

    Route::resource('attributes', AttributeController::class)->names([
        'index' => 'admin.attributes.index',
        'create' => 'admin.attributes.create',
        'store' => 'admin.attributes.store',
        'show' => 'admin.attributes.show',
        'edit' => 'admin.attributes.edit',
        'update' => 'admin.attributes.update',
        'destroy' => 'admin.attributes.destroy',
    ]);

    Route::post('attributes/{attribute}/values', [AttributeController::class, 'storeValue'])->name('attributes.values.store');
    Route::delete('attributes/values/{attributeValue}', [AttributeController::class, 'destroyValue'])->name('attributes.values.destroy');

    Route::resource('blogs', AdminBlogController::class)->names([
        'index' => 'admin.blogs.index',
        'create' => 'admin.blogs.create',
        'store' => 'admin.blogs.store',
        'show' => 'admin.blogs.show',
        'edit' => 'admin.blogs.edit',
        'update' => 'admin.blogs.update',
        'destroy' => 'admin.blogs.destroy',
    ]);

    Route::prefix('settings')->name('admin.settings.')->group(function () {
        Route::resource('payment', PaymentMethodController::class)->parameters(['payment' => 'payment']);
        Route::resource('shipping', ShippingRateController::class)->parameters(['shipping' => 'shipping']);
    });

    Route::resource('membership-tiers', MembershipTierController::class)->names([
        'index' => 'admin.membership-tiers.index',
        'create' => 'admin.membership-tiers.create',
        'store' => 'admin.membership-tiers.store',
        'show' => 'admin.membership-tiers.show',
        'edit' => 'admin.membership-tiers.edit',
        'update' => 'admin.membership-tiers.update',
        'destroy' => 'admin.membership-tiers.destroy',
    ]);


    Route::get('/warehouse', [WarehouseController::class, 'index'])->name('admin.warehouse.index');
    Route::get('/warehouse/create', [WarehouseController::class, 'create'])->name('admin.warehouse.create');
    Route::post('/warehouse', [WarehouseController::class, 'store'])->name('admin.warehouse.store');

    Route::controller(ReturnController::class)->prefix('returns')->name('admin.returns.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{return}', 'show')->name('show');
        Route::put('/{return}', 'update')->name('update');
    });


    Route::get('/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/export', [AdminOrderController::class, 'export'])->name('admin.orders.export');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
    Route::post('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');
    Route::get('orders/{order}/print', [AdminOrderController::class, 'print'])->name('admin.orders.print');
    Route::get('/revenue', [App\Http\Controllers\Admin\RevenueController::class, 'index'])->name('admin.revenue.index');
    Route::get('/reviews', [App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('admin.reviews.index');
    Route::delete('/reviews/{review}', [App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('admin.reviews.destroy');
});


Route::middleware(['auth', 'role:manager'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('staffs', StaffController::class)->except(['show']);
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('staffs/{staff}', [StaffController::class, 'show'])->name('staffs.show');
});


Route::fallback(function () {
    $homeUrl = url('/');
    $btnText = 'Trang chủ';

    $user = Auth::user();

    // if ($user) {
    //     $isInternalUser = false;

    //     if (isset($user->role) && in_array($user->role, ['manager', 'staff'])) {
    //         $isInternalUser = true;
    //     } elseif (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['manager', 'staff'])) {
    //         $isInternalUser = true;
    //     }

    //     if ($isInternalUser) {
    //         $homeUrl = route('admin.dashboard');
    //         $btnText = 'Về Dashboard';
    //     }
    // }
    if ($user && $user->hasAnyRole(['manager', 'staff'])) {
        $homeUrl = route('admin.dashboard');
        $btnText = 'Về Dashboard';
    }

    return response()->view('errors.404', [
        'homeUrl' => $homeUrl,
        'btnText' => $btnText
    ], 404);
});
