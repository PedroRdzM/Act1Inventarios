<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\DetalleVenta;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use DB;

class VentaController extends Controller
{
    public function index(Request $request){//funcion para mostrar el listado de las ventas
      
        if($request){
            //en la variable sql se almacena lo que se captura en el buscador
            $sql=trim($request->get('buscarTexto'));
            $ventas=Venta::join('clientes','ventas.idcliente','=','clientes.id')//join con la tabla clientes y con la de users
            ->join('users','ventas.idusuario','=','users.id')
            ->join('detalle_ventas','ventas.id','=','detalle_ventas.idventa')
             ->select('ventas.id','ventas.tipo_identificacion',//mostramos la informacion con el select
             'ventas.num_venta','ventas.fecha_venta','ventas.impuesto',
             'ventas.estado','ventas.total','clientes.nombre as cliente','users.nombre')
            ->where('ventas.num_venta','LIKE','%'.$sql.'%')
            ->orderBy('ventas.id','desc')
            ->groupBy('ventas.id','ventas.tipo_identificacion',
            'ventas.num_venta','ventas.fecha_venta','ventas.impuesto',
            'ventas.estado','ventas.total','clientes.nombre','users.nombre')
            ->paginate(10);
             
            //reedirige a la vista de ventas.index
            return view('venta.index',["ventas"=>$ventas,"buscarTexto"=>$sql]);
            
        }
      
 
     }
 
        public function create(){//metodo para el formulario para el registro de la venta
 
             /*listar las clientes en ventana modal*/
             $clientes=DB::table('clientes')->get();
            
             /*listar los productos en ventana modal*/
             $productos=DB::table('productos as prod')
             ->join('detalle_compras','prod.id','=','detalle_compras.idproducto')
             ->select(DB::raw('CONCAT(prod.codigo," ",prod.nombre) AS producto'),'prod.id','prod.stock','prod.precio_venta')
             ->where('prod.condicion','=','1')//Muestra productos que esten activados
             ->where('prod.stock','>','0')//Muestra Productos que tengan stock
             ->groupBy('producto','prod.id','prod.stock','prod.precio_venta')
             ->get(); 
 
            //reedirecciona los parametros anteriores a la plantilla "create"
             return view('venta.create',["clientes"=>$clientes,"productos"=>$productos]);
  
        }
 
         public function store(Request $request){
         
         
             try{
                //se hace el registro de la venta
                 DB::beginTransaction();
                 $mytime= Carbon::now('America/Monterrey');//variable para la zona de horarios
 
                 $venta = new Venta();
                 $venta->idcliente = $request->id_cliente;
                 $venta->idusuario = \Auth::user()->id;//se almacena el id del usuario que est?? logeado en ese momento
                 $venta->tipo_identificacion = $request->tipo_identificacion;
                 $venta->num_venta = $request->num_venta;
                 $venta->fecha_venta = $mytime->toDateString();
                 $venta->impuesto = "0.20";
                 $venta->total=$request->total_pagar;
                 $venta->estado = 'Registrado';
                 $venta->save();//guarda los datos
 
                 $id_producto=$request->id_producto;
                 $cantidad=$request->cantidad;
                 $descuento=$request->descuento;
                 $precio=$request->precio_venta;
 
                 
                 //Recorro todos los elementos
                 $cont=0;
     
                  while($cont < count($id_producto)){
 
                     $detalle = new DetalleVenta();
                     /*enviamos valores a las propiedades del objeto detalle*/
                     /*al idcompra del objeto detalle le envio el id del objeto venta, que es el objeto que se ingres?? en la tabla ventas de la bd*/
                     /*el id es del registro de la venta*/
                     $detalle->idventa = $venta->id;
                     $detalle->idproducto = $id_producto[$cont];
                     $detalle->cantidad = $cantidad[$cont];
                     $detalle->precio = $precio[$cont];
                     $detalle->descuento = $descuento[$cont];           
                     $detalle->save();
                     $cont=$cont+1;
                 }
                     
                 DB::commit();
 
             } catch(Exception $e){
                 
                 DB::rollBack();
             }
 
             return Redirect::to('venta');
         }
 
         public function show($id){
 
             
            
            
             /*mostrar venta*/
 
             
             $venta = Venta::join('clientes','ventas.idcliente','=','clientes.id')
             ->join('detalle_ventas','ventas.id','=','detalle_ventas.idventa')
             ->select('ventas.id','ventas.tipo_identificacion',
             'ventas.num_venta','ventas.fecha_venta','ventas.impuesto',
             'ventas.estado','clientes.nombre',
             DB::raw('sum(detalle_ventas.cantidad*precio - detalle_ventas.cantidad*precio*descuento/100) as total')
             )
             ->where('ventas.id','=',$id)
             ->orderBy('ventas.id', 'desc')
             ->groupBy('ventas.id','ventas.tipo_identificacion',
             'ventas.num_venta','ventas.fecha_venta','ventas.impuesto',
             'ventas.estado','clientes.nombre')
             ->first();
 
             /*mostrar detalles*/
             $detalles = DetalleVenta::join('productos','detalle_ventas.idproducto','=','productos.id')
             ->select('detalle_ventas.cantidad','detalle_ventas.precio','detalle_ventas.descuento','productos.nombre as producto')
             ->where('detalle_ventas.idventa','=',$id)
             ->orderBy('detalle_ventas.id', 'desc')->get();
             
             return view('venta.show',['venta' => $venta,'detalles' =>$detalles]);
         }
         
         public function destroy(Request $request){//cambia el estado de la venta  a anulado
 
             $venta = Venta::findOrFail($request->id_venta);
             $venta->estado = 'Anulado';
             $venta->save();
             return Redirect::to('venta');
 
        }
 
         public function pdf(Request $request,$id){
         
             $venta = Venta::join('clientes','ventas.idcliente','=','clientes.id')
             ->join('users','ventas.idusuario','=','users.id')
             ->join('detalle_ventas','ventas.id','=','detalle_ventas.idventa')
             ->select('ventas.id','ventas.tipo_identificacion',
             'ventas.num_venta','ventas.created_at','ventas.impuesto',
             'ventas.estado',DB::raw('sum(detalle_ventas.cantidad*precio - detalle_ventas.cantidad*precio*descuento/100) as total'),'clientes.nombre','clientes.tipo_documento','clientes.num_documento',
             'clientes.direccion','clientes.email','clientes.telefono','users.usuario')
             ->where('ventas.id','=',$id)
             ->orderBy('ventas.id', 'desc')
             ->groupBy('ventas.id','ventas.tipo_identificacion',
             'ventas.num_venta','ventas.created_at','ventas.impuesto',
             'ventas.estado','clientes.nombre','clientes.tipo_documento','clientes.num_documento',
             'clientes.direccion','clientes.email','clientes.telefono','users.usuario')
             ->take(1)->get();
 
             $detalles = DetalleVenta::join('productos','detalle_ventas.idproducto','=','productos.id')
             ->select('detalle_ventas.cantidad','detalle_ventas.precio','detalle_ventas.descuento',
             'productos.nombre as producto')
             ->where('detalle_ventas.idventa','=',$id)
             ->orderBy('detalle_ventas.id', 'desc')->get();
 
             $numventa=Venta::select('num_venta')->where('id',$id)->get();
             
             $pdf= \PDF::loadView('pdf.venta',['venta'=>$venta,'detalles'=>$detalles]);
             return $pdf->download('venta-'.$numventa[0]->num_venta.'.pdf');
         }
}
