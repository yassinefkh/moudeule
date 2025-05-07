<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cours extends Model
{
    use HasFactory;

    protected $table = 'cours';


    protected $fillable = [
        'intitule',
        'user_id',
        'formation_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'cours_users', 'cours_id', 'user_id');
    }

    public function plannings()
    {
        return $this->hasMany(Planning::class, 'cours_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'cours_users', 'cours_id', 'user_id');
    }


    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function annonces()
    {
        return $this->hasMany(Annonce::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class)->orderBy('ordre');
    }

    /**
     * Les contrôles associés à ce cours
     */
    public function controles()
    {
        return $this->hasMany(Controle::class, 'cours_id');
    }

    /**
     * Calcule la moyenne générale des notes pour ce cours pour un étudiant
     */
    public function moyenneForUser($userId)
    {
        $moyenneGenerale = 0;
        $totalCoefficients = 0;

        $controles = $this->controles;

        if ($controles->isEmpty()) {
            return null;
        }

        foreach ($controles as $controle) {
            $note = $controle->getNoteForUser($userId);

            if ($note) {
                $moyenneGenerale += $note->note * $controle->coefficient;
                $totalCoefficients += $controle->coefficient;
            }
        }

        if ($totalCoefficients == 0) {
            return null;
        }

        return round($moyenneGenerale / $totalCoefficients, 2);
    }

    /**
     * Récupère les étudiants inscrits à ce cours
     */
    public function getStudents()
    {
        return $this->students;
    }

}