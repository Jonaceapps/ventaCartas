<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Carta;
use App\Models\Coleccion;
use Illuminate\Http\Request;

class CartasController extends Controller
{
    public function altaCarta(Request $request, $id){

        $respuesta = ["status" => 1, "msg" => "", "msg2" => ""];
        $usuario = User::find($id);
        
        if($usuario && $request->usuario->rol == 'Administrador'){

            $datos = $request -> getContent();
            $datos = json_decode($datos); 
            $controlador = true;
              
            $carta = new Carta();
            $carta -> nombre = $datos->nombre;
            $carta -> descripcion = $datos->descripcion;

            if(isset($datos->coleccion) && !empty($datos->coleccion)){

                foreach ($datos->coleccion as $id) {
                    $coleccion = Coleccion::find($id);
                    if(!$coleccion)
                        $controlador = false;
                }
                if($controlador){
                    try {
                        $carta->save();
                        $respuesta["msg"] = "Carta Guardada";
                        foreach ($datos->coleccion as $id) {

                            $carta = Carta::find($carta->id);
                            $coleccion = Coleccion::find($id);
                            if ($carta && $coleccion){
                                $carta->colecciones()->attach($coleccion);
                                $respuesta["msg2"] = "Colecciones asignadas correctamente a la carta"; 
                            }
                        }
                    }catch (\Exception $e) {
                        $respuesta["status"] = 0;
                        $respuesta["msg"] = "Se ha producido un error".$e->getMessage();  
                    } 
                } else {
                    $respuesta["status"] = 0;
                    $respuesta["msg"] = "Alguna coleccion asocida a la carta no es valida o no existe, intentalo de nuevo";
                }
            } else {
                $respuesta["status"] = 0;
                $respuesta["msg"] = "No se ha asignado ninguna coleccion, intentalo de nuevo";
            }
        } else {
            $respuesta["status"] = 0;
            $respuesta["msg"] = "Usuario no encontrado o es posible que no tengas los permisos necesarios";
        }
    
        return response()->json($respuesta);
    }

    public function altaColeccion(Request $request, $id){

        $respuesta = ["status" => 1, "msg" => "", "msg2" => "", "msg3" => ""];
        $usuario = User::find($id);

        if($usuario && $request->usuario->rol == 'Administrador'){

            $datos = $request -> getContent();
            $datos = json_decode($datos); 
            $controlador = true;

            $coleccion = new Coleccion();
            $coleccion -> nombre = $datos->nombre;
            $coleccion -> simbolo = $datos->simbolo;
            $coleccion -> fecha_edicion = $datos->fecha_edicion;

            if(isset($datos->cartas) && !empty($datos->cartas)){

                foreach ($datos->cartas as $data) {
                    $data = get_object_vars($data);
                    if(array_key_exists("id",$data)){
                        $id = $data["id"];
                        $cartas = Carta::find($id);
                    }
                    if(!$cartas)
                        $controlador = false;
                }
                if($controlador){
                    try {
                        $coleccion->save();
                        $respuesta["msg"] = "Coleccion Guardada";
                        $coleccion = Coleccion::find($coleccion->id);

                        foreach ($datos->cartas as $data) {
                            $data = get_object_vars($data);
                            
                            if(array_key_exists("id",$data)){
                                $id = $data["id"];
                                $cartas = Carta::find($id);
                                if ($coleccion && $cartas){
                                    $coleccion->cartas()->attach($cartas);
                                    $respuesta["msg2"] = "Cartas asignadas correctamente a la coleccion"; 
                                }
                            } 
                            elseif(array_key_exists("nombre",$data) && array_key_exists("descripcion",$data)){
                                $carta = Carta::create([
                                    'nombre' => $data["nombre"],
                                    'descripcion' => $data["descripcion"],
                                ]);
                                $cartaNueva = Carta::find($carta->id);
                                if ($coleccion && $cartaNueva){
                                    $coleccion->cartas()->attach($cartaNueva);
                                    $respuesta["msg3"] = " Cartas creadas y asignadas correctamente a la coleccion"; 
                                }
                            }
                            
                        }
                    }catch (\Exception $e) {
                        $respuesta["status"] = 0;
                        $respuesta["msg"] = "Se ha producido un error".$e->getMessage();  
                    } 
                } else {
                    $respuesta["status"] = 0;
                    $respuesta["msg"] = "Alguna carta asocida a la coleccion no es valida o no existe, intentalo de nuevo";
                } 
            } else {
                $respuesta["status"] = 0;
                $respuesta["msg"] = "No se ha asignado ninguna carta, intentalo de nuevo";
            }
        } else {
            $respuesta["status"] = 0;
            $respuesta["msg"] = "Usuario no encontrado o es posible que no tengas los permisos necesarios";
        }
        return response()->json($respuesta);
    }
}
