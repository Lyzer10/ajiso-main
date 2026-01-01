<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DisputeAttachment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'dispute_id',
        'name',
        'path',
        'file_type',
    ];

    /**
     * Defining relationships
     */
    public function dispute()
    {
        return $this->belongsTo(Dispute::class);
    }
}
