<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Exceptions\ExamException;

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
        
        // Handle ExamException with appropriate responses
        $this->renderable(function (ExamException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'error_type' => $e->getErrorType(),
                    'context' => $e->getContext(),
                ], $e->getCode() ?: 400);
            }
            
            // Add specific error message and flash context data
            return redirect()->back()->withInput()
                ->with('error', $e->getMessage())
                ->with('error_context', $e->getContext())
                ->with('error_type', $e->getErrorType());
        });
    }
} 