<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicionVendor extends Model
{
    use HasFactory;
    
      protected $table = 'medicion_vendors';

    protected $fillable = [
        'vendor_id',
        'name',
        'description',
        'price',
        'quantity',
        'status',
    ];

    // ✅ Relationship with Vendor (User)
    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }
}
