<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;
    protected $table = 'topics';

    // Relacion de uno a muchos
    public function questions()
    {
        return $this->hasMany('App\Question');
    }
}
