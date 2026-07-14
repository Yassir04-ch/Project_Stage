<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobOffer extends Model
{
    protected $fillable = [
        'title', 'description', 'requirements', 'location',
        'contract_type', 'salary_min', 'salary_max',
        'status', 'deadline', 'created_by',
    ];

    protected $casts = [
        'deadline'   => 'date',
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function getIsOpenAttribute(): bool
    {
        if ($this->status !== 'open') {
            return false;
        }

        return !$this->deadline || $this->deadline->isFuture();
    }
}