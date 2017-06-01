<?php

namespace Appstract\RefererRedirector;

use Illuminate\Database\Eloquent\Model;

class RefererRedirect extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'referer_url',
        'redirect_url',
        'start_date',
        'end_date',
    ];

    /**
     * Dates.
     *
     * @var array
     */
    protected $dates = [
        'start_date',
        'end_date',
    ];
}
