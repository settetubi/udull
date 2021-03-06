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
                'identifier' => (int)$category->id,
                'name' => (string)$category->name,
                'description' => (string)$category->description
            ];
        }

        return [
            'identifier' => (int)$user->id,
            'name' => (string)$user->username,
            'email' => (string)$user->email,
            'verified' => $user->verified,
            'verification_token' => $user->verification_token,
            'admin' => $user->admin,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'deleted_at' => ( $user->deleted_at ? (string)$user->deleted_at : null ),
            'categories' => $cats
        ];
    }


    public static function getOriginalOrTransformedAttribute ( $name, $get = 'original' )
    {
        $tmp = [
            'identifier' => 'id',
            'name' => 'username',
            'email' => 'email',
            'verified' => 'verified',
            'admin' => 'admin',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
            'deleted_at' => 'deleted_at',
            'categories' => 'categories',
            'password' => 'password',
            'password_confirmation' => 'password_confirmation'
        ];

        if ( $get == 'transformed' )
            $tmp = array_flip( $tmp );

        return ( $tmp[$name] ?? null );
    }
}
