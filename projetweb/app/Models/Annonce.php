<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cours;

class Annonce extends Model
{
    use HasFactory;

    protected $fillable = [
        'cours_id',
        'titre',
        'contenu',
    ];

    public function cours()
    {
        return $this->belongsTo(Cours::class);
    }
}