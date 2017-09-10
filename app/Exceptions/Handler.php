<?php
namespace App\Exceptions;

use Crawler;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Foundation\Validation\ValidationException;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Support\Facades\Response;
use Illuminate\Session\TokenMismatchException;
use Redirect;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Utils;

/**
 * Class Handler.
 */
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        TokenMismatchException::class,
        //ModelNotFoundException::class,
        //AuthorizationException::class,
        //HttpException::class,
        //ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param \Exception $e
     *
     * @return bool|void
     */
    public function report(Exception $e)
    {
        if (!$this->shouldReport($e)) {
            return false;
        }

dd($e);


        // don't show these errors in the logs
        if ($e instanceof NotFoundHttpException) {
            /*if (Crawler::isCrawler()) {
                return false;
            }*/
            // The logo can take a few seconds to get synced between servers
            // TODO: remove once we're using cloud storage for logos
            if (Utils::isNinja() && strpos(request()->url(), '/logo/') !== false) {
                return false;
            }
            // Log 404s to a separate file
            $errorStr = date('Y-m-d h:i:s') . ' ' . request()->url() . "\n" . json_encode(Utils::prepareErrorData('PHP')) . "\n\n";
            @file_put_contents(storage_path('logs/not_found.log'), $errorStr, FILE_APPEND);
            return false;
        } elseif ($e instanceof HttpResponseException) {
            return false;
        }

        if (!Utils::isTravis()) {
            Utils::logError(Utils::getErrorString($e));
            $stacktrace = date('Y-m-d h:i:s') . ' ' . $e->getTraceAsString() . "\n\n";
            @file_put_contents(storage_path('logs/stacktrace.log'), $stacktrace, FILE_APPEND);
            return false;
        } else {
            return parent::report($e);
        }
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception $e
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        //if (config('app.debug') && ! $request->ajax()) {
            $whoops = new \Whoops\Run;
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);

            return $whoops->handleException($e);
        //}

        //return parent::render($request, $e);
    }



    /**
     * Create a Symfony response for the given exception.
     *
     * @param  \Exception  $e
     * @return mixed
     */
    protected function convertExceptionToResponse(Exception $e)
    {
        //if (config('app.debug')) {
            $whoops = new \Whoops\Run;
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);

            return response()->make(
                $whoops->handleException($e),
                method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500,
                method_exists($e, 'getHeaders') ? $e->getHeaders() : []
            );
        //}

        return parent::convertExceptionToResponse($e);
    }


}
