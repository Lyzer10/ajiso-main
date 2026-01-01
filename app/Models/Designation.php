<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class Designation extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'designation',
    ];

    /**
     * Defining relationships
     */

    //Getting the user that has the designation
    // public function user(){

    //     return $this->belongsTo(User::class, 'designation_id', 'id');
    // }
}
