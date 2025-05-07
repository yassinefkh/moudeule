<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'controle_id',
        'user_id',
        'note',
        'commentaire'
    ];

    /**
     * Le contrôle associé à cette note
     */
    public function controle()
    {
        return $this->belongsTo(Controle::class, 'controle_id');
    }

    /**
     * L'étudiant associé à cette note
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }



}