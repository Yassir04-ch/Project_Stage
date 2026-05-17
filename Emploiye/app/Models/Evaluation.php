<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'employee_id', 'evaluator_id',
        'score', 'comment', 'evaluated_at',
    ];
 
    protected $casts = [
        'evaluated_at' => 'date',
        'score'        => 'integer',
    ];
 
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
 
    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }
}
 
