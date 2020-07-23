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
            'id' => (int)$category->id,
            'name' => (string)$category->name,
            'description' => (string)$category->description,
            'created_at' => $category->created_at,
            'updated_at' => $category->updated_at,
            'deleted_at' => ( $category->deleted_at ? (string)$category->deleted_at : null )


        ];
    }
}
