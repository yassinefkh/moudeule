<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Formation;
use App\Models\Cours;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasFactory;

    public $timestamps = false;

    protected $hidden = ['mdp'];

    protected $passwordColumn = 'mdp';

    protected $fillable = ['nom', 'prenom', 'login', 'mdp', 'formation_id', 'type'];

    protected $attributes = [
        'type' => 'user'
    ];

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function cours(): HasMany
    {
        return $this->hasMany(Cours::class);
    }
    public function courses()
    {
        return $this->belongsToMany(Cours::class, 'cours_users', 'user_id', 'cours_id');
    }


    public function isAdmin()
    {
        return $this->type == 'admin';
    }

    public function setPasswordAttribute(string $value)
    {
        $this->attributes['mdp'] = bcrypt($value);
    }

    public function assignedCourses()
    {
        if ($this->type === 'enseignant') {
            return $this->hasMany(Cours::class, 'user_id');
        }

        return null;
    }


    /**
     * Les notes de cet utilisateur
     */
    public function notes()
    {
        return $this->hasMany(Note::class, 'user_id');
    }

    public function moyenneGenerale()
    {
        $moyenneGenerale = 0;
        $totalCoefficients = 0;
        $debug = [];

        $courses = $this->courses;

        if ($courses->isEmpty()) {
            $debug[] = "Aucun cours trouvé";
            return null;
        }

        foreach ($courses as $course) {
            $moyenne = $course->moyenneForUser($this->id);
            $debug[] = "Cours {$course->id} ({$course->intitule}): moyenne = " . ($moyenne ?? "NULL");

            if ($moyenne !== null) {
                $moyenneGenerale += $moyenne;
                $totalCoefficients += 1;
            }
        }

        $debug[] = "Total moyennes: {$moyenneGenerale}";
        $debug[] = "Total coefficients: {$totalCoefficients}";

        if ($totalCoefficients == 0) {
            $debug[] = "Aucun coefficient > 0";

            return null;
        }

        $result = round($moyenneGenerale / $totalCoefficients, 2);
        $debug[] = "Résultat final: {$result}";

        return $result;
    }
}