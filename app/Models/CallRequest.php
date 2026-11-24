<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallRequest extends Model
{
    use HasFactory;

    protected $table = 'call_requests';

    protected $fillable = [
        'user_id',
        'doctor_id',
        'call_type',
        'total_min',
        'deduction',
        'call_status',
        'call_start_time',
        'ended_at',
    ];

    protected $casts = [
        'total_min' => 'integer',
        'deduction' => 'float',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Customer
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Advisor / Doctor
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    // Check if call is completed
    public function isCompleted()
    {
        return $this->call_status === 'complete';
    }

    // Check if call is in progress
    public function isInProgress()
    {
        return $this->call_status === 'progress';
    }

    // Check if call is pending
    public function isPending()
    {
        return $this->call_status === 'pending';
    }

    // Get formatted duration
    public function getDurationAttribute()
    {
        return $this->total_min . ' min';
    }

    // Get formatted total amount
    public function getFormattedTotalAttribute()
    {
        return number_format($this->deduction, 2);
    }
}
