<?php

namespace App\Transformers;

use App\Category;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        //
    ];

    /**
     * A Fractal transformer.
     *
     * @param Category $category
     * @return array
     */
    public function transform(Category $category)
    {
        return [
            'identifier' => (int)$category->id,
            'name' => (string)$category->name,
            'description' => (string)$category->description,
            'created_at' => $category->created_at,
            'updated_at' => $category->updated_at,
            'deleted_at' => ( $category->deleted_at ? (string)$category->deleted_at : null )


        ];
    }

    public static function originalAttribute ( $name )
    {
        $tmp = [
            'identifier' => 'id',
            'name' => 'name',
            'description' => 'description',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
            'deleted_at' => 'deleted_at',
        ];

        return ( $tmp[$name] ?? null );
    }
}
