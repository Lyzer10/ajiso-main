<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DisputeStatus extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'dispute_status',
    ];

    /**
     * Defining relationships
     */

    // //Getting the dispute that has dispute status
    // public function dispute(){

    //     return $this->belongsTo(Dispute::class, 'dispute_id', 'id');
    // }
}
