<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TypeOfCase extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type_of_case',
    ];

    /**
     * Defining relationships
     */

    // //Getting the dispute that has type of case
    // public function dispute(){

    //     return $this->belongsTo(Dispute::class, 'type_of_case_id', 'id');
    // }
}
