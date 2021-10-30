<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use DB;

class ProductoController extends Controller
{
    public function index(Request $request)//funcion para poder listar los productos
    {
        //

        if($request){
            //si existe el campo  (request) lo obtiene y hace la peticion a la base de datos con el join se compatan las tablas de categorias y productos
            $sql=trim($request->get('buscarTexto'));
            $productos=DB::table('productos as p')
            ->join('categorias as c','p.idcategoria','=','c.id')//relacionamos con la tabla de categoria con el join
            ->select('p.id','p.idcategoria','p.nombre','p.precio_venta','p.codigo','p.stock','p.imagen','p.condicion','c.nombre as categoria')
            ->where('p.nombre','LIKE','%'.$sql.'%')
            ->orwhere('p.codigo','LIKE','%'.$sql.'%')
            ->orderBy('p.id','desc')
            ->paginate(10);
             
            //listar las categorias para asignar una categoria al producto
            $categorias=DB::table('categorias')
            ->select('id','nombre','descripcion')
            ->where('condicion','=','1')->get(); //solo obtiene las categorias que tengan la condicion activa=1
            //regresa la vista con los parametros
            return view('producto.index',["productos"=>$productos,"categorias"=>$categorias,"buscarTexto"=>$sql]);
         
           
        }
       
    }

    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)//funcion que permite hacer registros
    {
        //instancia del objeto producto para crear un nuevo registro
        $producto= new Producto();
        $producto->idcategoria = $request->id;
        $producto->codigo = $request->codigo;
        $producto->nombre = $request->nombre;
        $producto->precio_venta = $request->precio_venta;
        $producto->stock = '0';//stock por defecto
        $producto->condicion = '1';//condicion por defecto 1 que significa activo

        //
        if($request->hasFile('imagen')){

        //Get filename with the extension
        $filenamewithExt = $request->file('imagen')->getClientOriginalName();
        
        //Get just filename
        $filename = pathinfo($filenamewithExt,PATHINFO_FILENAME);
        
        //Get just ext
        $extension = $request->file('imagen')->guessClientExtension();
        
        //FileName to store
        $fileNameToStore = time().'.'.$extension;
        
        //Upload Image
        $path = $request->file('imagen')->storeAs('public/img/producto',$fileNameToStore);

       
        } else{

            $fileNameToStore="noimagen.jpg";
        }
        
        $producto->imagen=$fileNameToStore;



        $producto->save();
        return Redirect::to("producto");
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)//metododo para editar el producto
    {
        //busca el objeto producto deacuerdo el id_producto
        $producto= Producto::findOrFail($request->id_producto);
        $producto->idcategoria = $request->id;
        $producto->codigo = $request->codigo;
        $producto->nombre = $request->nombre;
        $producto->precio_venta = $request->precio_venta;
        $producto->stock = '0';
        $producto->condicion = '1';

        //Handle File Upload
       
        if($request->hasFile('imagen')){

            /*si la imagen que subes es distinta a la que está por defecto 
            entonces eliminaría la imagen anterior*/  
          if($producto->imagen != 'noimagen.jpg'){ 
            Storage::delete('public/img/producto/'.$producto->imagen);
          }

         
            //Get filename with the extension
          $filenamewithExt = $request->file('imagen')->getClientOriginalName();
          
          //Get just filename
          $filename = pathinfo($filenamewithExt,PATHINFO_FILENAME);
          
          //Get just ext
          $extension = $request->file('imagen')->guessClientExtension();
          
          //FileName to store
          $fileNameToStore = time().'.'.$extension;
          
          //Upload Image
          $path = $request->file('imagen')->storeAs('public/img/producto',$fileNameToStore);
          
           
           
        } else {
            
            $fileNameToStore = $producto->imagen; 
        }

         $producto->imagen=$fileNameToStore;
 
        $producto->save();
        return Redirect::to("producto");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        // funcion para activar desactivar el producto 
            $producto= Producto::findOrFail($request->id_producto);
//si el producto tiene condicion 1 osea activo cambia su estado a desactivado, osea valor 0
            if($producto->condicion=="1"){
                
                $producto->condicion= '0';
                $producto->save();
                return Redirect::to("producto");
        
            } else{
                //en caso contrario activa el producto cambiendo la condicion a 1
                $producto->condicion= '1';
                $producto->save();
                return Redirect::to("producto");

            }
    }
}
