<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\AiController;

// Route::middleware('auth:sanctum')->group(function () {
//     Route::post('/ai/chat', [AiController::class, 'chat']);
// });
/*x

|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// use App\Models\User;
// use Illuminate\Support\Facades\Hash;

// Route::post('/login', function (Request $request) {
//     $user = User::where('email', $request->email)->first();
//     if (!$user || !Hash::check($request->password, $user->password)) {
//         return response()->json(['error' => 'Sai email hoặc mật khẩu'], 401);
//     }
//     $token = $user->createToken('postman')->plainTextToken;
//     return ['token' => $token, 'user' => $user];
// });
