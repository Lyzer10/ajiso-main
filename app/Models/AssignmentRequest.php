<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'dispute_id',
        'reason_description',
        'staff_id',
        'request_status'
    ];

    /**
     * Defining relationship
     */

    // Get the dispute associated with the request
    public function dispute(){

        return $this->belongsTo(Dispute::class);
    }

    // Get the staff associated with the request
    public function staff(){

        return $this->belongsTo(Staff::class);
    }

    // Get the user associated with the request
    public function requestedBy(){

        return $this->hasOneThrough(User::class, Staff::class, 'id', 'id', 'staff_id', 'user_id');
    }
}
