<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Carta;
use App\Models\Coleccion;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
                $respuesta["msg"] = "Coleccion o cartas vacias, intentalo de nuevo";
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
                        if(!$cartas)
                        $controlador = false;
                    }
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
    //Asociar cartas a una coleccion posteriormente
    public function asociarCartaColeccion(Request $request, $id){

        $respuesta = ["status" => 1, "msg" => "", "msg2" => ""];
        $usuario = User::find($id);
        
        if($usuario && $request->usuario->rol == 'Administrador'){

            $datos = $request -> getContent();
            $datos = json_decode($datos); 
            $controlador = true;

            if(isset($datos->cartas) && !empty($datos->cartas) && isset($datos->coleccion) && !empty($datos->coleccion)){

                foreach ($datos->cartas as $id) {
                    $cartas = Carta::find($id);
                    if(!$cartas)
                        $controlador = false;
                }
                $coleccion = Coleccion::find($datos->coleccion);
                if(!$coleccion)
                    $controlador = false;
                
                if($controlador){
                    try {
                        $coleccion = Coleccion::find($datos->coleccion); 
                        foreach ($datos->cartas as $id) {
                            $carta = Carta::find($id);
                            if ($carta && $coleccion){
                                $coleccion->cartas()->syncWithoutDetaching($carta);
                                $respuesta["msg"] = "Cartas asignadas correctamente a la coleccion"; 
                            }
                        }
                    }catch (\Exception $e) {
                        $respuesta["status"] = 0;
                        $respuesta["msg"] = "Se ha producido un error".$e->getMessage();  
                    } 

                }else {
                    $respuesta["status"] = 0;
                    $respuesta["msg"] = "Alguna carta o la coleccion no es valida, intentalo de nuevo";
                } 
            } else {
                $respuesta["status"] = 0;
                $respuesta["msg"] = "No se ha asignado ninguna carta o coleccion, intentalo de nuevo";
            }
        }else {
            $respuesta["status"] = 0;
            $respuesta["msg"] = "Usuario no encontrado o es posible que no tengas los permisos necesarios";
        }
        return response()->json($respuesta);
    }
    //Poner a la venta una carta, solo particulares y prefesionales
    public function ventaCartas(Request $request, $id){
        $respuesta = ["status" => 1, "msg" => ""];
        $usuario = User::find($id);
        
        if($usuario && $request->usuario->rol == 'Particular' || $usuario && $request->usuario->rol == 'Profesional'){

            $datos = $request -> getContent();
            $datos = json_decode($datos); 
            
            $venta = new Venta();
            $venta -> nombre = $datos->nombre;
            $venta -> cantidad = $datos->cantidad;
            $venta -> precioTotal = $datos->precioTotal;
            //Aqui se asocia la venta a un usuario.
            if(isset($datos->usuario_asociado) && !empty($datos->usuario_asociado) && isset($datos->carta_asociada) && !empty($datos->carta_asociada) ){
                $user = User::find($datos->usuario_asociado);
                $carta = Carta::find($datos->carta_asociada);
                if($user && $carta){
                    $venta -> usuario_asociado = $datos->usuario_asociado;
                    $venta -> carta_asociada = $datos->carta_asociada;
                    try {
                        $venta->save();
                        $respuesta["msg"] = "Venta subida correctamente";
                    }catch (\Exception $e) {
                        $respuesta["status"] = 0;
                        $respuesta["msg"] = "Se ha producido un error".$e->getMessage();  
                    }  
                } else {
                    $respuesta["status"] = 0;
                    $respuesta["msg"] = "Usuario o carta no encontrada";
                }
            }
            else {
                $respuesta["status"] = 0;
                $respuesta["msg"] = "No se ha asociado ningun usuario o carta, vuelve a intentarlo";
            }

        } else {
            $respuesta["status"] = 0;
            $respuesta["msg"] = "Usuario no encontrado o es posible que no tengas los permisos necesarios";
        }
        return response()->json($respuesta);
    }
    //Listado por nombre de una carta para posteriormente poder ponerla a la venta
    public function listadoVenta(Request $request){

        $respuesta = ["status" => 1, "msg" => "", "msg2" => ""];
        
        if($request->usuario->rol == 'Particular' ||$request->usuario->rol == 'Profesional'){
            Log::info('Permisos Correctos');
            try {
                //Devuelve listado de cartas por nombre
                if($request -> has('nombre')){
                   $cartas = Carta::where('cartas.nombre','like','%'. $request -> input('nombre').'%')
                   ->get();
                   if($cartas && count($cartas) != 0){
                        Log::info('Coincidencias encontradas');
                        $respuesta["msg"] = "Resultados encontrados";
                        $respuesta['cartas'] = $cartas;
                   }
                   else {
                        Log::warning('No se han encontrado coincidencias');
                        $respuesta["status"] = 0;
                        $respuesta["msg"] = "No se ha econtrado ninguna coincidencia";
                   }
                } else {
                    Log::error('No se ha pasado el parametro nombre');
                    $respuesta["status"] = 0;
                    $respuesta["msg"] = "No se ha pasado ningun nombre";
                }
            }catch (\Exception $e) {
                $respuesta["status"] = 0;
                $respuesta["msg"] = "Se ha producido un error".$e->getMessage();  
            }
        } else {
            Log::error('Permisos no validos');
            $respuesta["status"] = 0;
            $respuesta["msg"] = "Usuario no encontrado o es posible que no tengas los permisos necesarios";
        }
        return response()->json($respuesta);
    }
    //Busqueda de cartas que estan a la venta para comprar filtradas por nombre.
    public function busquedaCompra(Request $request){

        $respuesta = ["status" => 1, "msg" => ""];
        try {
            //Devuelve listado de cartas por nombre
            if($request -> has('nombre')){
               $cartasEnVenta = Venta::join('usuarios', 'ventas.usuario_asociado', '=', 'usuarios.id')
               ->where('ventas.nombre','like','%'. $request -> input('nombre').'%')
               ->select('ventas.*', 'usuarios.nombre as vendedor')
               ->orderBy('ventas.precioTotal', 'asc')
               ->get();

               if(!$cartasEnVenta->isEmpty())
               $respuesta['cartas_en_venta'] = $cartasEnVenta;
               else 
               $respuesta["msg"] = "No se ha encontrado ninguna coincidencia";

            } else {
                $respuesta["status"] = 0;
                $respuesta["msg"] = "No se ha introducido nigun nombre";
            }

        }catch (\Exception $e) {
            $respuesta["status"] = 0;
            $respuesta["msg"] = "Se ha producido un error".$e->getMessage();  
        }
        return response()->json($respuesta);
    }
}
