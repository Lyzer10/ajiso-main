<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dispute extends Model
{
    use HasFactory, SoftDeletes, CascadeSoftDeletes;

    /**
     * The attributes of the children models that can be deleted.
     *
     * @var array 
     */
    protected $cascadeDeletes = ['activities', 'attachments'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'dispute_no',
        'reported_on',
        'beneficiary_id',
        'matter_to_court',
        'type_of_court',
        'problem_description',
        'where_reported',
        'service_experience',
        'how_did_they_help',
        'how_can_we_help',
        'defendant_names_addr',
        'staff_id',
        'type_of_service_id',
        'type_of_case_id',
        'dispute_status_id',
    ];

    /**
     * Defining relationships
     */

    // Get the beneficiary associated with the dispute
    public function beneficiary()
    {

        return $this->belongsTo(Beneficiary::class);
    }

    // Get the staff associated with the dispute
    public function staff()
    {

        return $this->belongsTo(Staff::class);
    }

    // Get the user associated with the dispute
    public function assignedTo()
    {

        return $this->hasOneThrough(User::class, Staff::class, 'id', 'id', 'staff_id', 'user_id');
    }

    // Get the beneficiary associated with the dispute
    public function reportedBy()
    {

        return $this->hasOneThrough(User::class, Beneficiary::class, 'id', 'id', 'beneficiary_id', 'user_id');
    }

    // Get the type of service that belongs the dispute
    public function typeOfService()
    {

        return $this->hasOne(TypeOfService::class, 'id', 'type_of_service_id');
    }

    // Get the type of case that belongs the dispute
    public function typeOfCase()
    {

        return $this->hasOne(TypeOfCase::class, 'id', 'type_of_case_id');
    }

    // Get the dispute status that belongs the dispute
    public function disputeStatus()
    {

        return $this->hasOne(DisputeStatus::class, 'id', 'dispute_status_id');
    }

    // Get dispute activities that belongs the dispute
    public function activities()
    {

        return $this->hasMany(DisputeActivity::class);
    }

    //Get the client clinic visits associated with staff
    public function counselingSheets()
    {

        return $this->hasManyThrough(CounselingSheet::class, DisputeActivity::class);
    }

    // Get dispute attachments
    public function attachments()
    {

        return $this->hasMany(DisputeAttachment::class);
    }
}
