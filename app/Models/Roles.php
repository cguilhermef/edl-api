<?php


namespace App\Models;


class Roles extends Base
{
    protected $table = 'roles';

    protected $fillable = ['name'];

    protected $hidden = ['created_at', 'updated_at'];
}