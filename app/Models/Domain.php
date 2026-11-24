<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    protected $table = 'domains';
    public $timestamps = true;

    protected $fillable = ['name'];
    protected $hidden = ['created_at', 'updated_at'];
}