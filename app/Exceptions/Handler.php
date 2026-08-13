<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {});
    }

    public function render($request, Throwable $e)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->renderApiException($e);
        }

        if ($e instanceof PostTooLargeException) {
            return redirect()->back()
                ->withInput($request->except('images'))
                ->withErrors(['images' => 'The uploaded file is too large. Each photo must be under 5 MB.']);
        }

        return parent::render($request, $e);
    }

    private function renderApiException(Throwable $e): JsonResponse
    {
        if ($e instanceof AuthenticationException) {
            return $this->apiError('Unauthorized', 401);
        }

        if ($e instanceof AuthorizationException) {
            return $this->apiError('Forbidden', 403);
        }

        if ($e instanceof ModelNotFoundException) {
            return $this->apiError('Not found', 404);
        }

        if ($e instanceof NotFoundHttpException) {
            return $this->apiError('Route not found', 404);
        }

        if ($e instanceof ValidationException) {
            return response()->json([
                'success' => false,
                'data' => ['errors' => $e->errors()],
                'error' => 'Validation failed',
            ], 422);
        }

        \Log::error($e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);

        return $this->apiError('Internal server error', 500);
    }

    private function apiError(string $message, int $status): JsonResponse
    {
        return response()->json([
            'success' => false,
            'data' => null,
            'error' => $message,
        ], $status);
    }
}
