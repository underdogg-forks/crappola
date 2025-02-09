<?php

namespace App\Exceptions;

use App\Http\Requests\Request;
use App\Libraries\Utils;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * Class Handler.
 */
class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [TokenMismatchException::class, ModelNotFoundException::class, ValidationException::class, //AuthorizationException::class,
        //HttpException::class,
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @return bool|void
     */
    public function report(Throwable $e)
    {
        if ( ! $this->shouldReport($e)) {
            return;
        }

        try {
            Log::error($e->getMessage(), ['exception' => $e]);
        } catch (Throwable $internalException) {
            error_log('Exception handler failed: ' . $internalException->getMessage());
        }

        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $e)
    {
        $value = \Request::header('X-Ninja-Token');

        if ($e instanceof ModelNotFoundException) {
            if (isset($value) && mb_strlen($value) > 1) {
                $headers  = \App\Libraries\Utils::getApiHeaders();
                $response = json_encode(['message' => 'record does not exist'], JSON_PRETTY_PRINT);

                return Response::make($response, 404, $headers);
            }

            return Redirect::to('/');
        }

        if ( ! class_exists('Utils')) {
            return parent::render($request, $e);
        }

        if ($e instanceof TokenMismatchException) {
            if ( ! in_array($request->path(), ['get_started', 'save_sidebar_state'])) {
                // https://gist.github.com/jrmadsen67/bd0f9ad0ef1ed6bb594e
                return redirect()->back()->withInput($request->except('password', '_token'))->with(['warning' => trans('texts.token_expired'), ]);
            }
        }

        if ($this->isHttpException($e)) {
            switch ($e->getStatusCode()) {
                // not found
                case 404:
                    if ($request->header('X-Ninja-Token') != '') {
                        //API request which has hit a route which does not exist

                        $error['error'] = ['message' => 'Route does not exist'];
                        $error          = json_encode($error, JSON_PRETTY_PRINT);
                        $headers        = Utils::getApiHeaders();

                        return response()->make($error, 404, $headers);
                    }
                    break;

                    // internal error
                case '500':
                    if ($request->header('X-Ninja-Token') != '') {
                        //API request which produces 500 error

                        $error['error'] = ['message' => 'Internal Server Error'];
                        $error          = json_encode($error, JSON_PRETTY_PRINT);
                        $headers        = Utils::getApiHeaders();

                        return response()->make($error, 500, $headers);
                    }
                    break;
            }
        }

        // In production, except for maintenance mode, we'll show a custom error screen
        if (Utils::isNinjaProd() && ! Utils::isDownForMaintenance() && ! ($e instanceof HttpResponseException) && ! ($e instanceof ValidationException) && ! ($e instanceof ValidationException)) {
            $data = ['error' => get_class($e), 'hideHeader' => true, ];

            return response()->view('error', $data, 500);
        }

        return parent::render($request, $e);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param Request                 $request
     * @param AuthenticationException $exception
     *
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $guard = array_get($exception->guards(), 0);

        switch ($guard) {
            case 'client':
                $url = '/client/login';
                break;
            default:
                $url = '/login';
                break;
        }

        return redirect()->guest($url);
    }
}
