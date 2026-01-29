<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Auth\Access\AuthorizationException;
use Spatie\Permission\Exceptions\UnauthorizedException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    // app/Exceptions/Handler.php

    public function render($request, Throwable $exception)
    {
        if (
            $exception instanceof AuthorizationException ||
            $exception instanceof UnauthorizedException ||
            $exception instanceof AccessDeniedHttpException
        ) {
            $data = $this->getHomeData(); 
            return response()->view('errors.403', $data, 403);
        }

    
        if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            $data = $this->getHomeData();
            return response()->view('errors.404', $data, 404);
        }

        return parent::render($request, $exception);
    }


    private function getHomeData()
    {
        $homeUrl = url('/');
        $btnText = 'Về Trang chủ';
        $user = Auth::guard('web')->user();

        if ($user) {
            $isInternal = false;
            if (isset($user->role) && in_array($user->role, ['manager', 'staff'])) {
                $isInternal = true;
            } elseif (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['manager', 'staff'])) {
                $isInternal = true;
            }

            if ($isInternal) {
                $homeUrl = route('admin.dashboard');
                $btnText = 'Về Dashboard';
            }
        }
        return ['homeUrl' => $homeUrl, 'btnText' => $btnText];
    }
}
