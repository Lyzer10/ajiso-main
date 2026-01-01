<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Staff extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'office',
        'is_assigned',
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

    //Get the user associated with staff
    public function user(){

        return $this->belongsTo(User::class);
    }

    //Get the disputes associated with staff
    public function disputes(){

        return $this->hasMany(Dispute::class);
    }

    //Get the disputes associated with staff
    public function disputesActivities(){

        return $this->hasManyThrough(DisputeActivity::class, Dispute::class);
    }
}
