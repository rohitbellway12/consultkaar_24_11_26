<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['item_id','item_type', 'quantity', 'user_id'];

    protected $casts = [
        'item_id' => 'integer',
        'item_type' => 'string',
        'quantity' => 'integer',
        'user_id' => 'integer'
    ];
    
    public function product()
    {
        return $this->belongsTo(Product::class, 'item_id');
    }
    
    public function lab_test()
    {
        return $this->belongsTo(LabTest::class, 'item_id');
    }
}
