<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proveedor;
use Illuminate\Support\Facades\Redirect;
use DB;

class ProveedorController extends Controller
{
  //
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //

        if($request){
            //lista los proveedores en tabla de orden desendente
            $sql=trim($request->get('buscarTexto'));
            $proveedores=DB::table('proveedores')
            ->where('nombre','LIKE','%'.$sql.'%')
            ->orwhere('num_documento','LIKE','%'.$sql.'%')
            ->orderBy('id','desc')
            ->paginate(8);
            return view('proveedor.index',["proveedores"=>$proveedores,"buscarTexto"=>$sql]);
            
        }
       
    }

    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)//funcion para registar proveedores
    {
        //instancia un objeto proveedor para poder realizar un nuevo registro
        $proveedor= new Proveedor();
        $proveedor->nombre = $request->nombre;
        $proveedor->tipo_documento = $request->tipo_documento;
        $proveedor->num_documento = $request->num_documento;
        $proveedor->telefono = $request->telefono;
        $proveedor->email = $request->email;
        $proveedor->direccion = $request->direccion;
        $proveedor->save();
        return Redirect::to("proveedor");//reedirecciona al formulario proveedor
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)//funcion para editar los proveedores
    {
        //encuentra el objeto proveedor mediante el id_proveedor
        $proveedor= Proveedor::findOrFail($request->id_proveedor);
        $proveedor->nombre = $request->nombre;
        $proveedor->tipo_documento = $request->tipo_documento;
        $proveedor->num_documento = $request->num_documento;
        $proveedor->telefono = $request->telefono;
        $proveedor->email = $request->email;
        $proveedor->direccion = $request->direccion;
        $proveedor->save();
        return Redirect::to("proveedor");//reedirecciona a la ventana proveedor
    }

}
