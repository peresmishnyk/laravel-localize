<?php

namespace Backpack\CRUD\Tests\Unit\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{

    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password'];

    public function getNameComposedAttribute()
    {
        return $this->name.'++';
    }
}
