<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Topic;


class TestController extends Controller
{
  /**
   *@return \Iluminate\View\View
   */
  public function testOrm()
  {
    // $questions = Question::all();
    // foreach ($questions as $question) {
    //   echo "<h1>" . $question->question . "</h1>";
    //   echo  "<h2>" . $question->answers . "</h2>";
    //   echo  "<h3>" . $question->id . "</h3>";
    //   echo "<span>{ $question->user}</span>";
    // }

    $topics = Topic::all();
    foreach ($topics as $topic) {
      echo "<h1>{$topic->topic_name}</h1>";
    }
    die();
  }
}
