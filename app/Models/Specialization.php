<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specialization extends Model
{
    protected $table = 'specializations';
    public $timestamps = true;

    protected $fillable = ['domain_id', 'name'];
    protected $hidden = ['created_at', 'updated_at'];

    // Optional: Relationship (agar future me chahiye ho)
    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }
}