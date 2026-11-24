<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithDrawRequest extends Model
{
    protected $fillable = [
        'request_by',
        'admin_id',
        'transaction_note',
        'amount',
        'approved',
        'status',
        'payment_method',
        'receipt_upload',
        'rejection_reason',
        'account_details',
        'note'
    ];
    protected $table = 'withdrawl_requests';

    // Customer relation (role_id = 2)
    public function customer()
    {
        return $this->belongsTo(User::class, 'request_by')->where('role_id', 2);
    }

    // Relationship to User (Advisor/Vendor)
    public function advisor()
    {
        return $this->belongsTo(User::class, 'request_by');
    }

    // Generic requester relation
    public function requester()
    {
        return $this->belongsTo(User::class, 'request_by');
    }
}
