<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function usuarios(Request $request)
    {
        return $this->hasMany('App\User');
    }

    public function register(Request $request)
    {
        // Recoger los datos del usuario por POST
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);


        if (!empty($params) && !empty($params_array)) {
            // Limpiar datos
            $params_array = array_map('trim', $params_array);
            // Validar datos
            $validate = Validator::make($params_array, [
                'name' => ['required'],
                'email' => ['required', 'email', 'unique:users'],
                'password' => ['required', Password::min(5)]
            ]);

            if ($validate->fails()) {
                // La validacion ha fallado
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => "The user hasn't been created correctly",
                    'errors' => $validate->errors()
                );
            } else {
                // Validacion pasada correctamente

                // Cifrar la constraseña
                $password_encrypted = hash('sha256', $params->password);

                // Crear el usuario
                $user = new User();
                $user->name = $params_array['name'];
                $user->email = $params_array['email'];
                $user->password = $password_encrypted;


                // Guardar el usuario
                try {
                    $user->save();
                } catch (\Throwable $th) {
                    return $th;
                    $data = array(
                        'status' => 'error',
                        'code' => 404,
                        'message' => "The data is not correctly saved in the DB",
                    );
                    return response()->json($data, $data['code']);
                }

                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => "The user has been created correctly",
                    'user' => $user
                );
            }
        } else {
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => "The data is not correctly send",
            );
        }

        return response()->json($data, $data['code']);
    }

    public function login(Request $request)
    {
        $jwtAuth = new JwtAuth();

        // Recibir datos por POST
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);



        // Validar datos
        $validate = Validator::make($params_array, [
            'email' => ['required'],
            'password' => ['required']
        ]);

        if ($validate->fails()) {
            // La validacion ha fallado
            $signup = array(
                'status' => 'error',
                'code' => 404,
                'message' => "The user hasn't able to login  correctly",
                'errors' => $validate->errors()
            );
        } else {
            // Cifrar la contraseña 
            $password = hash('sha256', $params->password);
            // Devolver el token o los datos
            $signup = $jwtAuth->signUp($params->email, $password);
            if (!empty($params->getToken)) {
                $signup = $jwtAuth->signUp($params->email, $password, true);
            }
        }



        return response()->json($signup, 200);
    }
    public function update(Request $request)
    {
        // Comprobar si el usuario esta identificado
        $token = $request->header('Authorization');
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);


        // Recoger los datos por POST
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);


        if ($checkToken && !empty($params_array)) {
            // Sacar usuario identificado
            $user = $jwtAuth->checkToken($token, true);

            // Validar los datos
            $validate = Validator::make($params_array, [
                'name' => ['required'],
                'email' => ['required', 'email', 'unique:users' . $user->sub],
                'password' => ['required', Password::min(5)]
            ]);
            // Quitar los campos que no se deben actualizar
            unset($params_array['id']);
            unset($params_array['created_at']);
            unset($params_array['remember_token']);
            unset($params_array['password']); //CONTRASEÑA NO SE ACTUALIZA

            // Actualizar el resultado en la BBD
            $user_updated = User::where('id', $user->sub)->update($params_array);

            // Devolver el array con el resultado
            $data = array(
                'code' => 200,
                'status' => 'success',
                'message' => 'The user is correctly updated',
                'user' => $user,
                'changes' => $params_array
            );
        } else {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'The user is not identified'
            );
        }
        return response()->json($data, $data['code']);
    }

    public function detail($id)
    {
        $user = User::find($id);

        if (is_object($user)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'user' => $user
            );
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                "message" => 'El usuario no existe'
            );
        }
        return response()->json($data, $data['code']);
    }
}
