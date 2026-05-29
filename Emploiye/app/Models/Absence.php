<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
 
    protected $fillable = [
        'user_id', 'date', 'status',
        'check_in', 'check_out', 'note', 'is_justified',
    ];
 
    protected $casts = [
        'date'         => 'date',
        'is_justified' => 'boolean',
    ];
 
    public function user()
    {
        return $this->belongsTo(User::class);
    }
 
    public function justification()
    {
        return $this->hasOne(Justification::class);
    }
 
 
    public function isAbsent(): bool {
         return $this->status === 'absent';
          }
    public function isLate(): bool  { 
        return $this->status === 'late'; 
        }
    public function isPresent(): bool
    {
        return $this->status === 'present';
    }
}
 
 
