<?php

namespace App\Models;
use Illuminate\Support\Facades\Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class Appointment extends Model
{
    protected $guarded = [];

    protected $table = 'appointments';

    // Define relationship with customer/user
    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Define relationship with doctor/user
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    // Scope for pending appointments
    public function scopePending($query)
    {


        $doctorId = Session::get('doctor_id') ? Session::get('doctor_id') : '';
        $roleId = Session::get('doctor_role_id') ? Session::get('doctor_role_id') :'';
     
        if ($doctorId && $roleId == '3') {
            return $query->where('status', 'pending')->where('doctor_id', $doctorId);
        } else {
            return $query->where('status', 'pending');
        }
    }

    // Scope for confirmed appointments
    public function scopeConfirmed($query)
    {   

        
        $doctorId = Session::get('doctor_id') ? Session::get('doctor_id') : '';
        $roleId = Session::get('doctor_role_id') ? Session::get('doctor_role_id') :'';
        
        if ($doctorId && $roleId == '3') {
            return $query->where('status', 'confirmed')->where('doctor_id', $doctorId);
        } else {
            return $query->where('status', 'confirmed');
        }
    }

    // Scope for rejected appointments
    public function scopeRejected($query)
    {
        
        $doctorId = Session::get('doctor_id') ? Session::get('doctor_id') : '';
        $roleId = Session::get('doctor_role_id') ? Session::get('doctor_role_id') :'';
        
        if ($doctorId && $roleId == '3') {
            return $query->where('status', 'rejected')->where('doctor_id', $doctorId);
        } else {
            return $query->where('status', 'rejected');
        }
    }

    // Scope for cancelled appointments
    public function scopeCancelled($query)
    {
        
        $doctorId = Session::get('doctor_id') ? Session::get('doctor_id') : '';
        $roleId = Session::get('doctor_role_id') ? Session::get('doctor_role_id') :'';
        
        if ($doctorId && $roleId == '3') {
            return $query->where('status', 'cancelled')->where('doctor_id', $doctorId);
        } else {
            return $query->where('status', 'cancelled');
        }
    }

    // Scope for completed appointments
    public function scopeCompleted($query)
    {
        
        $doctorId = Session::get('doctor_id') ? Session::get('doctor_id') : '';
        $roleId = Session::get('doctor_role_id') ? Session::get('doctor_role_id') :'';
        if ($doctorId && $roleId == '3') {
            return $query->where('status', 'completed')->where('doctor_id', $doctorId);
        } else {
            return $query->where('status', 'completed');
        }
    }

    // Scope for all appointments
    public function scopeAllAppointments($query)
    {
        
        $doctorId = Session::get('doctor_id') ? Session::get('doctor_id') : '';
        $roleId = Session::get('doctor_role_id') ? Session::get('doctor_role_id') :'';
        
        if ($doctorId && $roleId == '3') {
            return $query->whereIn('status', ['pending', 'confirmed', 'rejected', 'cancelled', 'completed'])
                         ->where('doctor_id', $doctorId);
        } else {
            return $query->whereIn('status', ['pending', 'confirmed', 'rejected', 'cancelled', 'completed']);
        }
    }
}
?>
