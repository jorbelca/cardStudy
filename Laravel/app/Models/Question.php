<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $table = 'questions';

    protected $fillable = ['question', 'answer1', 'answer2', 'answer3', 'topic_id', 'correct_answer'];
    // Relacion de muchos a uno
    public function user()
    {
        return $this->belongsTo('App\Models\User', "user_id");
    }
    public function question()
    {
        return $this->belongsTo('App\Models\Question', "creator_id");
    }
    public function user_record()
    {
        return $this->belongsTo('App\Models\User_Record', "user_id");
    }
    public function topic()
    {
        return $this->belongsTo('App\Models\Topic', "topic_id");
    }
}
