<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

trait ApiResponser
{
    private function successResponse($data, $code)
    {
        return response()->json( $data, $code );
    }

    protected function errorResponse( $message, $code )
    {
     return response()->json(['error' => $message, 'code' => $code], $code);
    }

    protected function showAll( Collection $collection, $code = 200 )
    {
        if ( empty( $collection ) )
            return $this->successResponse(['data' => $collection], $code);

        $collection = $this->transformData($collection, $collection->first()->transformer);
        return $this->successResponse(['data' => $collection], $code);
    }

    protected function showOne( Model $model, $code = 200 )
    {
        $model = $this->transformData($model, $model->transformer);

        return $this->successResponse(['data' => $model], $code);
    }

    protected function transformData( $data, $transformer )
    {
        return fractal( $data, new $transformer )->toArray()['data'];

    }
}
