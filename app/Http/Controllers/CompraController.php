<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Compra;
use App\Models\DetalleCompra;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB;

class CompraController extends Controller
{
    public function index(Request $request){
      
        if($request){
        //para mostrar el estado de las compras 
            $sql=trim($request->get('buscarTexto'));
            //con el join relacionamos los proveedores, user y detalle de compras 
            $compras=Compra::join('proveedores','compras.idproveedor','=','proveedores.id')
            ->join('users','compras.idusuario','=','users.id')
            ->join('detalle_compras','compras.id','=','detalle_compras.idcompra')
            //seleccionamos lo que vamos a mostrar
             ->select('compras.id','compras.tipo_identificacion',
             'compras.num_compra','compras.fecha_compra','compras.impuesto',
             'compras.estado','compras.total','proveedores.nombre as proveedor','users.nombre')
            ->where('compras.num_compra','LIKE','%'.$sql.'%')
            ->orderBy('compras.id','desc')
            ->groupBy('compras.id','compras.tipo_identificacion',
            'compras.num_compra','compras.fecha_compra','compras.impuesto',
            'compras.estado','compras.total','proveedores.nombre','users.nombre')
            ->paginate(10);//paginamos para mostrar de 10 en 10 registros
             
            //retorna la vista compras.index y le asignamos el parametro compras
            return view('compra.index',["compras"=>$compras,"buscarTexto"=>$sql]);
            
        }
      
 
     }
 
        public function create(){
 
             /*listar las proveedores en ventana modal*/
             $proveedores=DB::table('proveedores')->get();//llamamos a la tabla proveedores y enlista
            
             /*listar los productos en ventana modal*/
             $productos=DB::table('productos as prod')
             ->select(DB::raw('CONCAT(prod.codigo," ",prod.nombre) AS producto'),'prod.id')//concatenamos el codigo y nombre del producto y lo muestra como producto
             ->where('prod.condicion','=','1')->get(); //se mustran los productos que esten activados
            //
             return view('compra.create',["proveedores"=>$proveedores,"productos"=>$productos]);
  
        }
        //para crear el registro de la compra
         public function store(Request $request){
         
         
 
             try{
 
                 DB::beginTransaction();
                //toma la fecha y hora
                 $mytime= Carbon::now('America/Monterrey');
                //declaramos un objeto compra del modelo compra y llenamos los campos
                 $compra = new Compra();
                 $compra->idproveedor = $request->id_proveedor;
                 $compra->idusuario = \Auth::user()->id;//el id del usuario se toma del que este log en ese momento
                 $compra->tipo_identificacion = $request->tipo_identificacion;
                 $compra->num_compra = $request->num_compra;
                 $compra->fecha_compra = $mytime->toDateString();//comvertinos la fecha a string
                 $compra->impuesto = '0.20';
                 $compra->total = $request->total_pagar;
                 $compra->estado = 'Registrado';
                 $compra->save();
                //se toma de la vista 
                 $id_producto=$request->id_producto;
                 $cantidad=$request->cantidad;
                 $precio=$request->precio_compra;
                
                 
                 //Recorro todos los elementos
                 $cont=0;
                //si la varieble es menor al id del producto
                  while($cont < count($id_producto)){
 
                     $detalle = new DetalleCompra();
                     /*enviamos valores a las propiedades del objeto detalle*/
                     /*al idcompra del objeto detalle le envio el id del objeto compra, que es el objeto que se ingresó en la tabla compras de la bd*/
                     $detalle->idcompra = $compra->id;//se asigna el id de compra 
                     $detalle->idproducto = $id_producto[$cont];
                     $detalle->cantidad = $cantidad[$cont];
                     $detalle->precio = $precio[$cont];    
                     $detalle->save();
                     $cont=$cont+1;
                 }
                     
                 DB::commit();//se hace el registro
 
             } catch(Exception $e){
                 
                 DB::rollBack();
             }
             //redericciona a la vista compra
             return Redirect::to('compra');
         }
 
         public function show($id){
    
             /*mostrar la compra*/
 
             //delcaramos el objeto compra y creamos una relaccion con la tabla proveedor
             $compra = Compra::join('proveedores','compras.idproveedor','=','proveedores.id')
             ->join('detalle_compras','compras.id','=','detalle_compras.idcompra')
             ->select('compras.id','compras.tipo_identificacion',//seleccionamos lo que vamos a mostrar
             'compras.num_compra','compras.fecha_compra','compras.impuesto',
             'compras.estado',DB::raw('sum(detalle_compras.cantidad*precio) as total'),'proveedores.nombre')
             ->where('compras.id','=',$id)//id del registro que queremos mostrar
             ->orderBy('compras.id', 'desc')
             ->groupBy('compras.id','compras.tipo_identificacion',
             'compras.num_compra','compras.fecha_compra','compras.impuesto',
             'compras.estado','proveedores.nombre')
             ->first();
 
             /*mostrar detalles de la compra */
             $detalles = DetalleCompra::join('productos','detalle_compras.idproducto','=','productos.id')
             ->select('detalle_compras.cantidad','detalle_compras.precio','productos.nombre as producto')
             ->where('detalle_compras.idcompra','=',$id)//
             ->orderBy('detalle_compras.id', 'desc')->get();
             //retornamos a la vista compra.shows
             return view('compra.show',['compra' => $compra,'detalles' =>$detalles]);
         }
         //metodo que permite anular una compra
         public function destroy(Request $request){
 

                 $compra = Compra::findOrFail($request->id_compra);//busca la compra por el id
                 $compra->estado = 'Anulado';//cambiamos el estado de la compra a anulado
                 $compra->save();//guardamos    
                 return Redirect::to('compra');//redirecciona a la vista compra
 
     }
 
         public function pdf(Request $request,$id){
         
             $compra = Compra::join('proveedores','compras.idproveedor','=','proveedores.id')
             ->join('users','compras.idusuario','=','users.id')
             ->join('detalle_compras','compras.id','=','detalle_compras.idcompra')
             ->select('compras.id','compras.tipo_identificacion',
             'compras.num_compra','compras.created_at','compras.impuesto',DB::raw('sum(detalle_compras.cantidad*precio) as total'),
             'compras.estado','proveedores.nombre','proveedores.tipo_documento','proveedores.num_documento',
             'proveedores.direccion','proveedores.email','proveedores.telefono','users.usuario')
             ->where('compras.id','=',$id)
             ->orderBy('compras.id', 'desc')
             ->groupBy('compras.id','compras.tipo_identificacion',
             'compras.num_compra','compras.created_at','compras.impuesto',
             'compras.estado','proveedores.nombre','proveedores.tipo_documento','proveedores.num_documento',
             'proveedores.direccion','proveedores.email','proveedores.telefono','users.usuario')
             ->take(1)->get();
 
             $detalles = DetalleCompra::join('productos','detalle_compras.idproducto','=','productos.id')
             ->select('detalle_compras.cantidad','detalle_compras.precio',
             'productos.nombre as producto')
             ->where('detalle_compras.idcompra','=',$id)
             ->orderBy('detalle_compras.id', 'desc')->get();
 
             $numcompra=Compra::select('num_compra')->where('id',$id)->get();
             
             $pdf= \PDF::loadView('pdf.compra',['compra'=>$compra,'detalles'=>$detalles]);
             return $pdf->download('compra-'.$numcompra[0]->num_compra.'.pdf');
         }
 
}
