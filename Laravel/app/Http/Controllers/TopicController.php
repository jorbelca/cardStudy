<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;

class TopicController extends Controller
{

    public function __construct()
    {
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }


    public function index()
    {
        $topics = Topic::all();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'topics' => $topics
        ]);
    }


    public function show($id)
    {
        $topic = Topic::find($id);

        if (is_object($topic)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'topic' => $topic
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => "The topic doesn't exists"
            ];
        }
        return response()->json($data, $data['code']);
    }

    public function store(Request $request)
    {
        // Recoger los datos por POST
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if ($params_array) {
            // Validar los datos
            $validate = \Validator::make($params_array, [
                'topic_name' => 'required'
            ]);
            // Guardar la categoria
            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'success',
                    'message' => 'No se ha guardado la categoria.'
                ];
            } else {
                $topic = new Topic();
                $topic->topic_name = $params_array['topic_name'];
                $topic->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'topic' => $topic
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'success',
                'message' => 'No has enviado ninguna categoria.'
            ];
        }
        // Devolver el resultado
        return response()->json($data, $data['code']);
    }
    public function update($id, Request $request)
    {
        // Recoger los datos por POST
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {
            // Validar los datos
            $validate = \Validator::make($params_array, [
                'topic_name' => 'required'
            ]);
            // Quitar lo que no quiero actualizar
            unset($params_array['id']);
            unset($params_array['created_at']);

            // Actualizar el registro(categoria)
            $topic = Topic::where('id', $id)->update($params_array);

            $data = [
                'code' => 200,
                'status' => 'success',
                'topic' => $params_array
            ];
        } else {
            $data = [
                'code' => 400,
                'status' => 'success',
                'message' => 'No has enviado ninguna categoria.'
            ];
        }
        // Devolver respuesta 
        return response()->json($data, $data['code']);
    }
}
