<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;
use Illuminate\Support\Facades\Redirect;
use DB;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        if ($request){
            //hace la peticion a la base de datos con la variable de buscarTexto que esta en la visa 
            $sql=trim($request->get('buscarTexto'));
            $categorias=DB::table('categorias')->where('nombre','LIKE','%'.$sql.'%')
            ->orderBy('id','desc')
            ->paginate(10);
            //muestra el resultado en el front
            return view('categoria.index',["categorias"=>$categorias,"buscarTexto"=>$sql]);
            

        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //se inserta a la base de datos para crear una nueva categoria
        $categoria= new Categoria();
        $categoria->nombre= $request->nombre;
        $categoria->descripcion= $request->descripcion;
        $categoria->condicion= '1';
        $categoria->save();
        //redirecciona a la ruta categorias
        return Redirect::to("categoria");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //busca un registro que ya existe en la tabla mediante el id
        $categoria= Categoria::findOrFail($request->id_categoria);

        $categoria->nombre= $request->nombre;
        $categoria->descripcion= $request->descripcion;
        $categoria->condicion= '1';
        $categoria->save();
        //redirecciona
        return Redirect::to("categoria");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        //busca y obetenemos la categoria 
        $categoria= Categoria::findOrFail($request->id_categoria);
        //si la condicion es 1 cambiamos el campo condicion a 0
        if($categoria->condicion=="1"){
            
            $categoria->condicion= '0';
            $categoria->save();
            return Redirect::to("categoria");
    //si esta desactivado lo activa
        } else{

            $categoria->condicion= '1';
            $categoria->save();
            return Redirect::to("categoria");

        }
    }
}
