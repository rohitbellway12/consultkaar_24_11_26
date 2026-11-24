<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'cities';
    public $timestamps = true;

    protected $fillable = ['state_id', 'name'];
    protected $hidden = ['created_at', 'updated_at'];

    // Optional: Relationship
    public function state()
    {
        return $this->belongsTo(State::class);
    }
}