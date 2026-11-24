<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $table = 'states';
    public $timestamps = true;

    protected $fillable = ['country_id', 'name'];
    protected $hidden = ['created_at', 'updated_at'];

    // Optional: Relationship
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}