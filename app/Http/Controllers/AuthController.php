<?php

namespace App\Http\Controllers;

use \stdClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Persona;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Constants\Estado;
use App\Constants\TipoUsuario;

class AuthController extends Controller
{
    public function register(Request $request){
        $estado=Estado::ACTIVO;
        $validator=Validator::make($request->all(),[
          'name'=>'required|string|max:255',
          'email'=>'required|string|email|max:255|unique:users',
          'password'=>'required|string|min:8',
          'tipo' => 'required|string|max:50',

          'apellido' => 'required|string|max:255',
          'telefono' => 'nullable|string|max:255',
          'direccion' => 'nullable|string|max:255',
          'coordenadas' => 'nullable|string|max:200',
          'observacion' => 'nullable|string|max:300',
          'foto_url' => 'nullable|string|max:200'
        ]);

        if ($validator->fails()){
            return response()->json($validator->errors());
        }

        // Verificar que el tipo de usuario sea válido
        if (!in_array($request->tipo, TipoUsuario::getValues())) {
            return response()->json([
                'error' => 'Tipo de usuario no válido'
            ], 400);
        }
        $abogado_id=0;
        //Cuando se esta registrando un abogado dependiente
        if ($request->tipo===TipoUsuario::ABOGADO_DEPENDIENTE)
        {
           $abogado_id=Auth::user()->id;
        }
        //Cuando se esta registrando un procurador
        $datosJson=[];
        if ($request->tipo===TipoUsuario::PROCURADOR)
        {
            $datosJson=[
                'NO_MOTO' => true,
                'SI_MANEJA_NO_TIENE' => false,
                'SI_MOTO' => false,
            ];
        }

        $user=User::create([
           'name'=>$request->name,
           'email'=>$request->email,
           'password'=>Hash::make($request->password),
           'tipo'=>$request->tipo,
           'abogado_id'=> $abogado_id,
           'opciones_moto'=>json_encode($datosJson),
           'estado'=>$estado,
           'es_eliminado'=>0
        ]);

        $persona = Persona::create([
            'nombre'=>$request->name,
            'apellido'=>$request->apellido,
            'telefono'=>$request->telefono,
            'direccion'=>$request->direccion,
            'coordenadas'=>$request->coordenadas,
            'observacion'=>$request->observacion,
            'foto_url'=>$request->foto_url,
            'estado'=>$estado,
            'es_eliminado'=>0,
            'usuario_id'=>$user->id,

          ]);
          $user->load('persona');

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()
               ->json(['data'=>$user,'access_token'=>$token,'token_type'=>'Bearer']);
    }

    public function login(Request $request){
        if (!Auth::attempt($request->only('email', 'password'))){
            return response()
                   ->json(['message' => 'No autorizado'], 401);
        }
        $user = User::where('email', $request['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()
               ->json([
                'message'=> 'Hi '.$user->name,
                'accessToken'=>$token,
                'token_type'=>'Bearer',
                'user'=>$user,
               ]);
    }

    public function logout(){
        auth()->user()->tokens()->delete();
        return [
            'message'=>'Cierre de session exitoso'
        ];
    }
}
