<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    use ApiResponser;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request, Exception $exception)
    {
        if ( $exception instanceof ModelNotFoundException ) {
            $modelName = strtolower(class_basename(
                $exception->getModel()
            ));

            return $this->errorResponse("$modelName id not found", '404');
        }

        if ( $exception instanceof NotFoundHttpException ){
            return $this->errorResponse("Not Found", "404");
        }

        if ( $exception instanceof MethodNotAllowedHttpException ){
            return $this->errorResponse("Not Allowed", "405");
        }

        if ( $exception instanceof HttpException ){
            return $this->errorResponse($exception->getMessage(), $exception->getStatusCode());
        }

//        if ( $exception instanceof QueryException ){
//            if ( $exception->errorCode[1] == 1451 )
//                return $this->errorResponse("Cannot remove this resource permanently. It is related to any other resource", 409);
//        }

        if ( config('app.debug') )
            return parent::render($request, $exception);

        return $this->errorResponse('Unexcpected Exception try again later', 500);

    }

    /**
     * Convert a validation exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Validation\ValidationException  $exception
     * @return \Illuminate\Http\JsonResponse
     */
    protected function invalid($request, ValidationException $exception)
    {
        $errors = $exception->validator->errors()->getMessages();

        return $this->errorResponse($errors, 422);

    }


}
