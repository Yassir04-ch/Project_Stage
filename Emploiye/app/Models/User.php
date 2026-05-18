<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
 
      protected $fillable = [
        'role_id',
        'firstname',
        'lastname',
        'cin',
        'telephone',
        'adresse',
        'genre',
        'date_naissance',
        'date_embauche',
        'type_contrat',
        'salaire',
        'photo',
        'email',
        'password',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'date_naissance'    => 'date',
        'date_embauche'     => 'date',
        'salaire'           => 'decimal:2',
    ];
 
 
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
 
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'employee_skill')
                    ->withPivot('level')
                    ->withTimestamps();
    }
 
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'assignments', 'employee_id', 'project_id')
                    ->withPivot('role_in_project', 'start_date', 'end_date')
                    ->withTimestamps();
    }
 
    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'employee_id');
    }
 
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'employee_id');
    }
 
    public function givenEvaluations()
    {
        return $this->hasMany(Evaluation::class, 'evaluator_id');
    }
 
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
 
    public function absences()
    {
        return $this->hasMany(Absence::class);
    }
 
 
 
    public function justificationsGiven()
    {
        return $this->hasMany(Justification::class, 'justified_by');
    }
 

 
 
}
 
 
