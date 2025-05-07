<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Controle extends Model
{
    use HasFactory;

    protected $fillable = [
        'cours_id',
        'titre',
        'description',
        'date_controle',
        'coefficient'
    ];

    protected $dates = [
        'date_controle',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'date_controle' => 'datetime',
    ];

    /**
     * Le cours associé à ce contrôle
     */
    public function cours()
    {
        return $this->belongsTo(Cours::class, 'cours_id');
    }

    /**
     * Les notes associées à ce contrôle
     */
    public function notes()
    {
        return $this->hasMany(Note::class, 'controle_id');
    }

    /**
     * Calcule la moyenne des notes pour ce contrôle
     */
    public function moyenne()
    {
        $notes = $this->notes()->pluck('note');
        
        if ($notes->isEmpty()) {
            return null;
        }
        
        return round($notes->avg(), 2);
    }

    /**
     * Vérifie si un étudiant a une note pour ce contrôle
     */
    public function hasNoteForUser($userId)
    {
        return $this->notes()->where('user_id', $userId)->exists();
    }

    /**
     * Récupère la note d'un étudiant pour ce contrôle
     */
    public function getNoteForUser($userId)
    {
        return $this->notes()->where('user_id', $userId)->first();
    }
}