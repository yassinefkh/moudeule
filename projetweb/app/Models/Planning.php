<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Planning extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'date_debut',
        'date_fin',
    ];

    public function cours()
    {
        return $this->belongsTo(Cours::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id', Cours::class);
    }

}