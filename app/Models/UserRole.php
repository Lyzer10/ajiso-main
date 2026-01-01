<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserRole extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role_abbreviation',
        'role_name',
    ];

    /**
     * Defining relationships
     */

    // Getting the user that has the role
    // public function user(){

    //     return $this->belongsTo(User::class);
    // }

}
