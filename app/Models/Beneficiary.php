<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Beneficiary extends Model
{
    use HasFactory, Notifiable, SoftDeletes, CascadeSoftDeletes;

    /**
     * The attributes of the children models that can be deleted.
     *
     * @var array
     */
    protected $cascadeDeletes = ['disputes'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'gender',
        'disabled',
        'registration_source',
        'date_of_birth',
        'education_level_id',
        'address',
        'district_id',
        'ward',
        'street',
        'survey_choice_id',
        'marital_status_id',
        'form_of_marriage',
        'marriage_date',
        'no_of_children',
        'financial_capability',
        'employment_status',
        'monthly_income',
        'user_id',
    ];

    /**
     * Route notifications for the mail channel.
     *
     * @return string
     */
    public function routeNotificationForMail()
    {
        return $this->user->email;
    }

    /**
     *  Defining Relationships
     */

    //Get the user associated with beneficiary
    public function user()
    {

        return $this->belongsTo(User::class)->where('is_active', 1)->where('deleted_at', NULL);
    }

    //Get the district associated with the beneficiary
    public function district()
    {

        return $this->hasOne(District::class, 'id', 'district_id');
    }

    //Get the region associated with the beneficiary through district
    public function region()
    {

        return $this->hasOneThrough(Region::class, District::class, 'id', 'id', 'district_id', 'region_id');
    }


    //Get the education level associated with the beneficiary
    public function educationLevel()
    {

        return $this->hasOne(EducationLevel::class, 'id', 'education_level_id');
    }

    //Get the maritalStatus associated with the beneficiary
    public function maritalStatus()
    {

        return $this->hasOne(MaritalStatus::class, 'id', 'marital_status_id');
    }

    //Get the survey choice associated with the beneficiary
    public function surveyChoice()
    {

        return $this->hasOne(SurveyChoice::class, 'id', 'survey_choice_id');
    }

    //Get the survey choice associated with the beneficiary
    public function income()
    {

        return $this->hasOne(Income::class, 'id', 'income_id');
    }

    //Get the employment status associated with the beneficiary
    public function employmentStatus()
    {

        return $this->hasOne(EmploymentStatus::class, 'id', 'employment_status_id');
    }

    //Get the marriage form associated with the beneficiary
    public function marriageForm()
    {

        return $this->hasOne(MarriageForm::class, 'id', 'marriage_form_id');
    }

    //Get the disputes associated with beneficiary
    public function disputes()
    {

        return $this->hasMany(Dispute::class);
    }
}
