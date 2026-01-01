<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DisputeFile extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'path',
        'file_type',
        'counseling_sheet_id',
    ];

    /**
     * Defining relationships
     */

    //Getting the dispute files that has counseling sheet
    public function counselingSheet(){

        return $this->belongsTo(CounselingSheet::class, 'counseling_sheet_id', 'id');
    }
}
