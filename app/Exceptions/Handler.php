<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
// Consider importing specific exceptions if you plan to handle them in the register method
// use Illuminate\Auth\AuthenticationException;
// use Illuminate\Validation\ValidationException;
// use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
  /**
   * A list of exception types with their corresponding custom log levels.
   * For example, to log a specific type of exception as 'warning' instead of 'error'.
   *
   * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
   */
  protected $levels = [
    // \App\Exceptions\CustomNonCriticalException::class => \Psr\Log\LogLevel::INFO,
  ];

  /**
   * A list of the exception types that are not reported (i.e., not logged or sent to error trackers).
   * Useful for exceptions that are common and handled gracefully, or represent expected conditions.
   *
   * @var array<int, class-string<\Throwable>>
   */
  protected $dontReport = [
    // \Illuminate\Auth\AuthenticationException::class,
    // \Illuminate\Auth\Access\AuthorizationException::class,
    // \Symfony\Component\HttpKernel\Exception\HttpException::class,
    // \Illuminate\Database\Eloquent\ModelNotFoundException::class,
    // \Illuminate\Validation\ValidationException::class,
  ];

  /**
   * A list of the inputs that are never flashed to the session on validation exceptions.
   * This is a security measure to prevent sensitive data like passwords from being re-displayed.
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
   * This method is called when the exception handler is registered.
   */
  public function register(): void
  {
    $this->reportable(function (Throwable $e) {
      // Example: Send exceptions to an external service like Sentry
      // if (app()->bound('sentry') && $this->shouldReport($e)) {
      //     app('sentry')->captureException($e);
      // }

      // Example: Add custom context to logs
      // Log::error($e->getMessage(), ['user_id' => auth()->id(), 'trace' => $e->getTraceAsString()]);
    });

    // Example: Registering a renderable callback for a specific exception type
    // This allows you to customize the HTTP response for certain exceptions.
    // Your system design mentions a MiscErrorController for custom error pages[cite: 23].
    // You might route specific exceptions to that controller or render specific views here.
    /*
        $this->renderable(function (\App\Exceptions\CustomApplicationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 500);
            }
            // return response()->view('errors.custom-application-error', ['exception' => $e], 500);
            // Or redirect to a route handled by MiscErrorController
            // return redirect()->route('misc.error.show', ['errorCode' => 'custom_error_code']);
        });

        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Resource not found.'], 404);
            }
            // Laravel will automatically look for resources/views/errors/404.blade.php
            // Your web.php also has a fallback route which might handle this.
        });
        */
  }

  /**
   * Prepare a JSON response for the given exception.
   * You can override this to customize JSON error responses globally.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Throwable  $e
   * @return \Illuminate\Http\JsonResponse
   */
  /*
    protected function prepareJsonResponse($request, Throwable $e)
    {
        return response()->json([
            'message' => $this->isHttpException($e) ? $e->getMessage() : 'Server Error',
            // Optionally include more details in development
            // 'exception' => get_class($e),
            // 'file' => $e->getFile(),
            // 'line' => $e->getLine(),
            // 'trace' => config('app.debug') ? $this->convertExceptionToArray($e)['trace'] : null,
        ], $this->isHttpException($e) ? $e->getStatusCode() : 500);
    }
    */
}
