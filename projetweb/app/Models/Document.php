<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cours;

class Document extends Model
{
    use HasFactory;

    protected $fillable = ['cours_id', 'section_id', 'titre', 'fichier'];


    public function cours()
    {
        return $this->belongsTo(Cours::class);
    }

    public function section()
{
    return $this->belongsTo(Section::class);
}

}