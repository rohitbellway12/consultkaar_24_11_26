<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\Domain;


class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    // app/Models/User.php

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'documents' => 'array',
        'identity_proofs' => 'array',
        'relevant_documents' => 'array',
    ];


    public function getNameAttribute($value)
    {
        if (empty($value)) {
            return 'N/A';
        }
        return trim(preg_replace('/^Dr\.\s*/i', '', $value));
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    // Define a relationship method to associate users with other related models, if necessary.

    public function isAdmin()
    {
        return $this->role_id === '1';
    }

    public function isDoctor()
    {
        return $this->role_id === '3';
    }


    //-----------------Rifaul changes--------------------//
    public function bankDetails()
    {
        return $this->hasOne(Bank::class, 'doctor_id', 'id');
    }

    public function medicions()
    {
        return $this->hasMany(MedicionVendor::class, 'vendor_id');
    }

    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function speciality()
    {
        return $this->belongsTo(Speciality::class, 'speciality_id');
    }

    public function callRequests()
    {
        return $this->hasMany(CallRequest::class, 'doctor_id');
    }
}
// use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Foundation\Auth\User as Authenticatable;
// use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;
// use Tymon\JWTAuth\Contracts\JWTSubject;
// use App\Models\Domain;


// class User extends Authenticatable implements JWTSubject
// {
//     use HasApiTokens, HasFactory, Notifiable;

//     /**
//      * The attributes that are mass assignable.
//      *
//      * @var array<int, string>
//      */
//     protected $guarded = [];


//     /**
//      * The attributes that should be hidden for serialization.
//      *
//      * @var array<int, string>
//      */
//     protected $hidden = [
//         'password',
//         'remember_token',
//     ];

//     /**
//      * The attributes that should be cast.
//      *
//      * @var array<string, string>
//      */
//     protected $casts = [
//         'email_verified_at' => 'datetime',
//     ];



//      public function domain()
//   {
//       return $this->belongsTo(Domain::class, 'domain_id');
//   }

//     /**
//      * Get the identifier that will be stored in the subject claim of the JWT.
//      *
//      * @return mixed
//      */
//     public function getJWTIdentifier()
//     {
//         return $this->getKey();
//     }

//     /**
//      * Return a key value array, containing any custom claims to be added to the JWT.
//      *
//      * @return array
//      */
//     public function getJWTCustomClaims()
//     {
//         return [];
//     }

//     // Define a relationship method to associate users with other related models, if necessary.

//     public function isAdmin()
//     {
//         return $this->role_id === '1';
//     }

//     public function isDoctor()
//     {
//         return $this->role_id === '3';
//     }


//     //-----------------Rifaul changes--------------------//
//     public function bankDetails()
//     {
//     return $this->hasOne(Bank::class, 'doctor_id', 'id');
//     }


//     public function speciality()
// {
//   return $this->belongsTo(Speciality::class, 'speciality_id');
//   }



//     public function medicions()
// {
//     return $this->hasMany(MedicionVendor::class, 'vendor_id');
// }







// }