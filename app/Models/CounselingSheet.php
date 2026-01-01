<?php

namespace App\Models;

use App\Models\DisputeFile;
use App\Models\DisputeActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CounselingSheet extends Model
{
    use HasFactory, SoftDeletes, CascadeSoftDeletes;

    /**
     * The attributes of the children models that can be deleted.
     *
     * @var array 
     */
    protected $cascadeDeletes = ['files'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'attended_at',
        'time_in',
        'time_out',
        'appointment',
        'advice_given',
        'dispute_activity_id',
    ];

    /**
     * Defining relationships
     */

    //Getting the counseling sheet that has dispute activities
    public function disputeActivity(){

        return $this->belongsTo(DisputeActivity::class, 'dispute_activity_id', 'id');
    }

    //Getting the dispute files that has belong to this sheet
    public function files(){

        return $this->hasMany(DisputeFile::class);
    }
}
