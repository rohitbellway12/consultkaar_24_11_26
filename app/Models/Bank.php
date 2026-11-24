<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{

    protected $table = 'doctor_banks';
    
    // Rifaul Changes
     protected $fillable = [
        'doctor_id',
        'account_number',
        'bank_name',
        'ifsc_code',
        'account_holder_name',
        "account_proof_file",
         'upi_id'
        
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'doctor_id', 'id');
    }

    protected $guarded = [];

}
