<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    protected $primaryKey = 'institution_id';
    protected $fillable = ['name', 'type', 'location'];

    public function departments() { return $this->hasMany(Department::class, 'institution_id'); }
}