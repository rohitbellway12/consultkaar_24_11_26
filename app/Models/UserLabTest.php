<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLabTest extends Model
{

    protected $fillable = [];

    protected $table = 'users_lab_tests';


    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}

