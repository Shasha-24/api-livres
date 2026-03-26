<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exemplaire extends Model
{
    use HasFactory;

    protected $table = 'exemplaires';

    protected $fillable = [
        'livre_id',
        'code_barre',
        'etat',
        'statut',
    ];


    public function livre()
    {
        return $this->belongsTo(Livre::class);
    }
}
