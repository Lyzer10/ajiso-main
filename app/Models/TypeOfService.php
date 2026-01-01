<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TypeOfService extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'service_abbreviation',
        'type_of_service',
    ];

    /**
     * Defining relationships
     */

    // //Getting the dispute that has type of service
    // public function dispute(){

    //     return $this->belongsTo(Dispute::class, 'type_of_service_id', 'id');
    // }
}
