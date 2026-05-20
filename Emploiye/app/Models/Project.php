<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'name', 'description', 'budget',
        'start_date', 'end_date', 'status',
    ];
 
    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'budget'     => 'decimal:2',
    ];
 
 
    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
 
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }
}
