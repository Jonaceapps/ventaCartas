<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Models\Carta;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ValidarPermisoUsuario
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $respuesta = ["status" => 1, "msg" => ""];
        if($request->usuario->rol == 'Administrador' 
        || $request->usuario->rol == 'Profesional' || $request->usuario->rol == 'Particular'){
            $respuesta["msg"] = "Todo Ok, tienes los permisos";
            return $next($request);
        } else {
            $respuesta["status"] = 0;
            $respuesta["msg"] = "Este usuario no tiene los permisos";  
        }
        return response()->json($respuesta);  
    }
}
