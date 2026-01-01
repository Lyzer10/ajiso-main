<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MetricMeasure extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'metric_measure',
    ];

    /**
     * Defining relationships
     */

    //Getting the metric that has the metric measure
    public function metric(){

        return $this->belongsTo(Metric::class, ' metric_measure_id', 'id');
    }
}
