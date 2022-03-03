<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Carta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UsuariosController extends Controller
{
    //REGISTRO
    public function registro(Request $req){

        $respuesta = ["status" => 1, "msg" => ""];
        $validator = Validator::make(json_decode($req->
        getContent(),true), [
            "nombre" => 'required|unique:App\Models\User,nombre|max:50',
            "email" => 'required|email|unique:App\Models\User,email|max:30',
            "pass" => 'required|regex:/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{6,}/',
            "rol" => 'required|in:Administrador,Profesional,Particular'
        ]);

        if($validator -> fails()){
            $respuesta["status"] = 0;
            $respuesta["msg"] = $validator->errors(); 
        } else {

            $datos = $req -> getContent();
            $datos = json_decode($datos); 

            $usuario = new User();
            $usuario -> nombre = $datos -> nombre;
            $usuario -> email = $datos -> email;
            $usuario -> pass = Hash::make($datos->pass);
            $usuario -> rol = $datos -> rol;
            
            try {
                $usuario->save();
                $respuesta["msg"] = "Usuario Guardado";
            }catch (\Exception $e) {
                $respuesta["status"] = 0;
                $respuesta["msg"] = "Se ha producido un error".$e->getMessage();  
            }
        }  
        return response()->json($respuesta);
    }
    //LOGIN
    public function login(Request $req){

        $respuesta = ["status" => 1, "msg" => ""];

        $username = $req->nombre;
        $usuario = User::where('nombre', $username) -> first();

        if ($usuario){

            if (Hash::check($req->pass, $usuario -> pass)){

                do {
                    $token = Hash::make($usuario->id.now());
                } while(User::where('api_token', $token) -> first());

                $usuario -> api_token = $token;
                $usuario -> save();
                $respuesta["msg"] = "Login correcto, tu api token es: ".$usuario -> api_token;  

            } else {
                $respuesta["status"] = 0;
                $respuesta["msg"] = "La contraseña no es correcta";  
            }

        } else {
            $respuesta["status"] = 0;
            $respuesta["msg"] = "Usuario no encontrado";  
        }
        return response()->json($respuesta);  
    }
       //Recuperar Contraseña
       public function recoverPass(Request $req){

        $respuesta = ["status" => 1, "msg" => ""];
        $datos = $req -> getContent();
        $datos = json_decode($datos); 
    
        $email = $datos->email;
        $usuario = User::where('email', $email) -> first();

        if($usuario){
            
            $caracteres = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
            $caracteresLenght = strlen($caracteres);
            $longitud = 8;
            $newPassword = "";
          
            for($i = 0; $i<$longitud; $i++) {
               $newPassword .= $caracteres[rand(0, $caracteresLenght -1)];
            }
            $usuario->api_token = null;
            $usuario->pass = Hash::make($newPassword);
            $usuario -> save();
            $respuesta["msg"] = "Tu contraseña nueva es: ".$newPassword;  

        } else {
            $respuesta["status"] = 0;
            $respuesta["msg"] = "Usuario no encontrado";  
        }

        return response()->json($respuesta);  

    }
}
