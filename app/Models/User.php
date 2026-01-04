<?php

namespace App\Models;

use App\Models\UserRole;
use App\Models\Designation;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, SoftDeletes, CascadeSoftDeletes, Notifiable;

    /**
     * The attributes of the children models that can be deleted.
     *
     * @var array 
     */
    protected $cascadeDeletes = ['staff', 'beneficiary'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_no',
        'name',
        'email',
        'password',
        'salutation_id',
        'first_name',
        'middle_name',
        'last_name',
        'tel_no',
        'role',
        'is_active',
        'image',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     *  Defining Relationships
     */

    //Get the designation associated with the user
    public function designation()
    {

        return $this->belongsTo(Designation::class, 'salutation_id');
    }

    //Get the role associated with the user
    public function role()
    {

        return $this->belongsTo(UserRole::class, 'user_role_id', 'id');
    }

    //Get the staff associated with the user
    public function staff()
    {

        return $this->hasOne(Staff::class);
    }

    // Get disputes assigned to the user role staff
    public function staffDisputes()
    {

        return $this->hasManyThrough(Dispute::class, Staff::class);
    }

    // Get disputes associated with the user through beneficiary
    public function beneficiaryDisputes()
    {

        return $this->hasManyThrough(Dispute::class, Beneficiary::class);
    }
}
