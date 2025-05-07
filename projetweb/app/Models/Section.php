<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    public $timestamps = false;

    protected $fillable = ['titre', 'cours_id', 'ordre'];

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function cours()
    {
        return $this->belongsTo(Cours::class);
    }
}
