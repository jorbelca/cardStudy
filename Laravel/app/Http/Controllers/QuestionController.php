<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{

    public function __construct()
    {
        $this->middleware('api.auth', ['except' => ['index', 'show', 'getQuestionsByTopic', 'getQuestionsByCreator']]);
    }

    public function index()
    {
        $questions = Question::all()->load('topic');

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'questions' => $questions
        ], 200);
    }

    public function show($id)
    {
        $question = Question::find($id)->load('topic');


        if (is_object($question)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'question' => $question
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => "The question does not exist"
            ];
        }
        return response()->json($data, $data['code']);
    }
    public function store(Request $request)
    {
        // Recoger datos por POST
        $json = $request->input('json');
        $params = json_decode($json);
        $params_array = json_decode($json, true);


        if (!empty($params_array)) {

            // Conseguir el usuario identificado
            $user = $this->getIdentity($request);


            // Validar los datos
            $validate = Validator::make($params_array, [
                'question' => ['required'],
                'topic_id' => ['required'],
                'correct_answer' => ['required'],
                'answer1' => ['required'],
                'answer2' => ['required'],
                'answer3' => ['required']
            ]);

            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => "The question has not been saved, it lacks the complete data"
                ];
            } else {
                // Guardar la pregunta
                $question = new Question();
                $question->creator_id = $user->sub;
                $question->topic_id = $params->topic_id;
                $question->question = $params->question;
                $question->correct_answer = $params->correct_answer;
                $question->answer1 = $params->answer1;
                $question->answer2 = $params->answer2;
                $question->answer3 = $params->answer3;
                $question->n_correct = 0;
                $question->n_incorrect = 0;

                $question->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'question' => $question
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => "The question could not been saved"
            ];
        }
        // Devolver respuesta

        return response()->json($data, $data['code']);
    }

    public function update($id, Request $request)
    {
        // Recoger los datos por POST
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);


        $data = array(
            'code' => 400,
            'status' => 'error',
            'message' => 'Data send incorrectly or you are not the creator'
        );

        if (!empty($params_array)) {
            // Validar los datos
            $validate = Validator::make($params_array, [
                'question' => 'required',
                'correct_answer' => 'required',
                'answer1' => 'required',
                'answer2' => 'required',
                'answer3' => 'required'
            ]);

            if ($validate->fails()) {
                $data['errors'] = $validate->errors();
                return response()->json($data, $data['code']);
            }
            // Eliminar lo que no queremos actualizar
            unset($params_array['id']);
            unset($params_array['creator_id']);
            unset($params_array['n_correct']);
            unset($params_array['n_incorrect']);
            unset($params_array['created_at']);
            unset($params_array['updated_at']);

            // Conseguir el usuario identificado
            $user = $this->getIdentity($request);
            // Buscar el registro
            $question  = Question::where('id', $id)->where('creator_id', $user->sub)->first();

            // Actualizar el registro en concreto
            // $where = [
            //     'creator_id' => $user->sub,
            //     'id' => $id
            // ];
            // DB::table('questions')->update($where, $params_array);
            if (!empty($question) && is_object($question)) {
                // Actualizar el registro en concreto
                $question->update($params_array);
                $question_updated = DB::table('questions')->where('id', $id)->get();
                // Devolver
                $data = array(
                    'code' => 200,
                    'status' => 'success',
                    'question' => $question_updated,
                    'changes' => $params_array
                );
            }
        }


        return response()->json($data, $data['code']);
    }
    public function destroy($id, Request $request)
    {
        // Conseguir el usuario identificado
        $user = $this->getIdentity($request);

        // Conseguir el registro
        $question  = Question::where('id', $id)->where('creator_id', $user->sub)->first();
        if (!empty($question)) {
            // Borrarlo 
            $question->delete();
            // Devolver
            $data = [
                "code" => 200,
                "status" => "success",
                "question" => $question
            ];
        } else {
            $data = [
                "code" => 404,
                "status" => "error",
                "message" => "The question does not exist or you are not the creator"
            ];
        }
        return response()->json($data, $data['code']);
    }

    private function getIdentity(Request $request)
    {
        // Conseguir el usuario identificado
        $jwtAuth = new JwtAuth();
        $token = $request->header('Authorization', null);
        $user = $jwtAuth->checkToken($token, true);

        return $user;
    }

    public function getQuestionsByTopic($topic_id)
    {
        $questions = Question::where('topic_id', $topic_id)->get();

        return response()->json([
            'status' => 'success',
            'questions' => $questions
        ], 200);
    }
    public function getQuestionsByCreator($creator_id)
    {
        $questions = Question::where('topic_id', $creator_id)->get();

        return response()->json([
            'status' => 'success',
            'questions' => $questions
        ], 200);
    }
}
