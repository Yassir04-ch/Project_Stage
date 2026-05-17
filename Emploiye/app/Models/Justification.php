<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Justification extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'absence_id', 'justified_by',
        'type', 'reason', 'proof_file', 'status',
    ];
 
    public function absence()
    {
        return $this->belongsTo(Absence::class);
    }
 
    public function justifiedBy()
    {
        return $this->belongsTo(User::class, 'justified_by');
    }
 
    public function isApproved(): bool { 
        return $this->status === 'approved';
         }
    public function isPending(): bool  { 
        return $this->status === 'pending'; 
        }
}
 

