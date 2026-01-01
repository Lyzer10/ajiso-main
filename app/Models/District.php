<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class District extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'district',
        'region_id',
    ];

    /**
     * Defining relationships
     */

    //Getting the region that has district
    public function region(){

        return $this->belongsTo(Region::class);
    }
}
