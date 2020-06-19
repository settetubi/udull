<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    const MAX_LEN_NAME = 50;
    const MAX_LEN_DESCRIPTION = 150;

    protected $fillable = [
        'name',
        'description'
    ];

    public function Users ()
    {
        return $this->belongsToMany(User::class);
    }
}
