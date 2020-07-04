<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{

    use SoftDeletes;

    const MAX_LEN_NAME = 50;
    const MAX_LEN_DESCRIPTION = 150;

    protected $dates = ['deleted_at'];
    protected $fillable = [
        'name',
        'description'
    ];

    protected $hidden = [
        'pivot'
    ];

    public function Users ()
    {
        return $this->belongsToMany(User::class);
    }
}
