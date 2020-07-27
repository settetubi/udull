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

        $trasformerObject = $collection->first()->transformer;
        $collection = $this->filterData( $collection, $trasformerObject );
        $collection = $this->sortData( $collection, $trasformerObject );
        $collection = $this->transformData($collection, $trasformerObject);
        return $this->successResponse(['data' => $collection], $code);
    }

    protected function showOne( Model $model, $code = 200 )
    {
        $model = $this->transformData($model, $model->transformer);

        return $this->successResponse(['data' => $model], $code);
    }

    protected function sortData( Collection $data, $transformer )
    {

        if ( request()->has('by') ) {
            $by = $transformer::originalAttribute(request()->by);
            $order = request()->has('order') && (in_array(request()->order, ['asc', 'desc'])) ?
                request()->order == 'desc':
                'asc';

            return $data->sortBy($by, SORT_REGULAR, $order == 'desc');
        }

        return $data;
    }

    protected function transformData( $data, $transformer )
    {
        return fractal( $data, new $transformer )->toArray()['data'];

    }

    protected function filterData ( Collection $data, $transformer )
    {
        foreach( request()->query() as $filter => $value ){
            $attribute = $transformer::originalAttribute( $filter );

            if ( isset( $attribute, $value) )
                $data = $data->where($attribute, $value);
        }

        return $data;
    }
}
