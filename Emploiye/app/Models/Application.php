<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'job_offer_id', 'full_name', 'email', 'phone',
        'cover_letter', 'cv_path', 'status',
    ];

    public function jobOffer()
    {
        return $this->belongsTo(JobOffer::class);
    }
}