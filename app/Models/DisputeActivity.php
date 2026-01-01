<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DisputeActivity extends Model
{
    use HasFactory, SoftDeletes, CascadeSoftDeletes;

    /**
     * The attributes of the children models that can be deleted.
     *
     * @var array 
     */
    protected $cascadeDeletes = ['counselingSheets'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'dispute_activity',
        'description',
        'activity_type',
        'dispute_id',
        'staff_id',
    ];

    /**
     * Defining relationships
     */

    //Getting the dispute that has dispute activities
    public function dispute(){

        return $this->belongsTo(Dispute::class, 'dispute_id', 'id');
    }

    //Getting the staff that has dispute activities
    public function staff(){

        return $this->belongsTo(Staff::class, 'staff_id', 'id');
    }

    //Getting the counseling sheet that has dispute activities
    public function counselingSheets(){

        return $this->hasMany(CounselingSheet::class);
    }
}
