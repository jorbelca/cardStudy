<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;



class JwtAuth
{
  public function signUp($email, $password, $getToken = null)
  {
    $key = getenv('TOKEN_KEY');
    // Buscar si existe el usuario con sus credenciales
    $user =  User::where([
      'email' => $email,
      'password' => $password
    ])->first();

    // Comprobar si son correctas
    $signup = false;
    if (is_object($user)) {
      $signup = true;
    }
    // Generar el token con los datos del usuario identificado
    if ($signup) {
      $token = array(
        'sub' => $user->id,
        'email' => $user->email,
        'name' => $user->name,
        'iat' => time(),
        'exp' => time() + (5 * 24 * 60 * 60)
      );



      $jwt = JWT::encode($token, $key, 'HS256');
      $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

      // Devolver los datos decodificados o el token, en funcion del parametro
      if (is_null($getToken)) {
        $data = $jwt;
      } else {
        $data = $decoded;
      }
    } else {
      $data = array(
        'status' => 'error',
        'message' => 'Login incorrecto'
      );
    }

    return $data;
  }

  public function checkToken($jwt, $getIdentity = false)
  {
    $key = getenv('TOKEN_KEY');
    $auth = false;
    try {
      $jwt = str_replace('"', '', $jwt);
      $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
    } catch (\UnexpectedValueException $exception) {
      $auth = false;
    } catch (\DomainException $exception) {
      $auth = false;
    }
    if (!empty($decoded) && is_object($decoded) && isset($decoded->sub)) {
      $auth = true;
    } else {
      $auth = false;
    }

    if ($getIdentity) {
      return $decoded;
    }
    return $auth;
  }
}
