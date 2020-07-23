<?php

namespace App\Transformers;

use App\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
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
     * @param User $user
     * @return array
     */
    public function transform(User $user)
    {
        $cats = [];
        foreach( $user->categories as $category ){
            $cats[] = [
                'id' => (int)$category->id,
                'name' => (string)$category->name,
                'description' => (string)$category->description
            ];
        }

        return [
            'id' => (int)$user->id,
            'username' => (string)$user->username,
            'email' => (string)$user->email,
            'verified' => $user->verified,
            'admin' => $user->admin,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'deleted_at' => ( $user->deleted_at ? (string)$user->deleted_at : null ),
            'categories' => $cats

        ];
    }
}
