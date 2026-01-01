<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Metric extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'metric',
        'metric_measure_id',
        'metric_limit',
    ];

    /**
     * Defining relationships
     */

    //Getting the metric measure associated with metric
    public function metricMeasure(){

        return $this->hasOne(MetricMeasure::class, 'id', 'metric_measure_id');
    }
}
