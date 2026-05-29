<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{ 
    protected $fillable = [
        'employee_id', 'project_id',
        'role_in_project', 'start_date', 'end_date',
    ];
 
    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];
 
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
 
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}

