<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Preference extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sys_abbr',
        'sys_name',
        'org_abbr',
        'org_name',
        'org_site',
        'org_email',
        'org_tel',
        'currency_format',
        'date_format',
        'language',
        'notification_mode',
    ];

    /**
     * Defining relationships
     */
}
