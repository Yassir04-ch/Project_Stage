<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

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
        'status',
        'service_id'
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
 
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

 
 
}
 
 
