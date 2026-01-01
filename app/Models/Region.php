<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Region extends Model
{
    use HasFactory, SoftDeletes, CascadeSoftDeletes;

    /**
     * The attributes of the children models that can be deleted.
     *
     * @var array 
     */
    protected $cascadeDeletes = ['districts'];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'region',
    ];

    /**
     * Defining relationships
     */

    //Getting the districts that belong to region
    public function districts(){

        return $this->hasMany(District::class);
    }
}
