<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $primaryKey = 'department_id';
    protected $fillable = ['institution_id', 'name', 'faculty'];

    public function institution() { return $this->belongsTo(Institution::class, 'institution_id'); }
    public function users() { return $this->hasMany(User::class, 'department_id'); }
}