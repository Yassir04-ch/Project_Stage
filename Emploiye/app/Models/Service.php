<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'description', 'chef_id'];

    public function chef()
    {
        return $this->belongsTo(User::class, 'chef_id');
    }

    public function employees()
    {
        return $this->hasMany(User::class, 'service_id');
    }
}