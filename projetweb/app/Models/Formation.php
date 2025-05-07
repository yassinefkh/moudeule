<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Cours;

class Formation extends Model
{
    use HasFactory;

    protected $fillable = ['intitule'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function cours()
    {
        return $this->hasMany(Cours::class);
    }

    public function students()
    {
        return $this->hasMany(User::class)->where('type', 'etudiant');
    }

    public function courses()
    {
        return $this->hasMany(Cours::class);
    }



}