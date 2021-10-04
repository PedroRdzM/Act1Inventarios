<!-- extenciuon de la plantilla principal-->
@extends('principal')
@section('contenido')


<main class="main">

 <div class="card-body">

 <h2>Agregar Compra</h2>

 <span><strong>(*) Campo obligatorio</strong></span><br/>

 <h3 class="text-center">LLenar el formulario</h3>
<!-- comienzo del formulario -->
<!-- llamada del metodo store del controllador de compra por el metodo post -->
    <form action="{{route('compra.store')}}" method="POST">
    {{csrf_field()}}<!--codigo para evitar ataques -->

            <div class="form-group row">

            <div class="col-md-8">  

                <label class="form-control-label" for="nombre">Nombre del Proveedor</label>
                
                    <select class="form-control selectpicker" name="id_proveedor" id="id_proveedor" data-live-search="true">
                                                    
                    <option value="0" disabled>Seleccione</option>
                    <!--ciclo para mostrar los nombres de los proveedores-->
                    @foreach($proveedores as $prove)
                    
                    <option value="{{$prove->id}}">{{$prove->nombre}}</option>
                            
                    @endforeach

                    </select>
                
                </div>
            </div>

            <div class="form-group row">

                <div class="col-md-8">  

                        <label class="form-control-label" for="documento">Documento</label>
                        <!-- campo para seleccionar el documentos con 2 opciones -->
                        <select class="form-control" name="tipo_identificacion" id="tipo_identificacion" required>
                                                        
                            <option value="0" disabled>Seleccione</option>
                            <option value="FACTURA">Factura</option>
                            <option value="TICKET">Ticket</option>
                            

                        </select>
                </div>
            </div>


            <div class="form-group row">

                <div class="col-md-8">
                        <label class="form-control-label" for="num_compra">Número Compra</label>
                        <!-- campo del nimero de compra -->
                        <input type="text" id="num_compra" name="num_compra" class="form-control" placeholder="Ingrese el número compra" required pattern="[0-9]{0,15}">
                </div>
            </div>

            <br/><br/>

            <div class="form-group row border">

                 <div class="col-md-8">  

                        <label class="form-control-label" for="nombre">Producto</label>
                            <!-- seccion para mostrar los productos que estan activos -->
                            <select class="form-control selectpicker" name="id_producto" id="id_producto" data-live-search="true">
                                                            
                            <option value="0" selected>Seleccione</option>
                            <!-- ciclo para mostrar los productos-->
                            @foreach($productos as $prod)
                            
                            <option value="{{$prod->id}}">{{$prod->producto}}</option>
                                    
                            @endforeach

                            </select>

                </div>

            </div>

            <div class="form-group row">
            <!-- seccion de cantidad del producto-->
                <div class="col-md-3">
                        <label class="form-control-label" for="cantidad">Cantidad</label>
                        
                        <input type="number" id="cantidad" name="cantidad" class="form-control" placeholder="Ingrese cantidad" pattern="[0-9]{0,15}">
                </div>
            <!-- seccion del precio de la compra de los pordictos-->
                <div class="col-md-3">
                        <label class="form-control-label" for="precio_compra">Precio Compra</label>
                        
                        <input type="number" id="precio_compra" name="precio_compra" class="form-control" placeholder="Ingrese precio de compra" pattern="[0-9]{0,15}">
                </div>

               
            <!-- boton para agregar el detalle de la compra -->
                <div class="col-md-3">
                        
                    <button type="button" id="agregar" class="btn btn-primary"><i class="fa fa-plus fa-2x"></i> Agregar detalle</button>
                </div>


            </div>

            <br/><br/>

           <div class="form-group row border">
<!-- seccion pra mostrar las compras a proveedores -->
              <h3>Lista de Compras a Proveedores</h3>

              <div class="table-responsive col-md-12">
                <table id="detalles" class="table table-bordered table-striped table-sm">
                <thead>
                    <tr class="bg-success">
                        <th>Eliminar</th>
                        <th>Producto</th>
                        <th>Precio(Mxn$)</th>
                        <th>Cantidad</th>
                        <th>SubTotal (Mxn$)</th>
                    </tr>
                </thead>
                 <!-- seccion de el cobro total de los productos-->
                <tfoot>
                  

                    <tr>
                        <th  colspan="4"><p align="right">TOTAL:</p></th>
                        <th><p align="right"><span id="total">Mxn$ 0.00</span> </p></th>
                    </tr>

                    <tr>
                        <th colspan="4"><p align="right">TOTAL IMPUESTO (20%):</p></th>
                        <th><p align="right"><span id="total_impuesto">Mxn$ 0.00</span></p></th>
                    </tr>

                    <tr>
                        <th  colspan="4"><p align="right">TOTAL PAGAR:</p></th>
                        <th><p align="right"><span align="right" id="total_pagar_html">Mxn$ 0.00</span> <input type="hidden" name="total_pagar" id="total_pagar"></p></th>
                    </tr>  

                </tfoot>

                <tbody>
                </tbody>
                
                
                </table>
              </div>
            
            </div>

            <div class="modal-footer form-group row" id="guardar">
            <!-- boton para registrar todas la compras -->
            <div class="col-md">
               <input type="hidden" name="_token" value="{{csrf_token()}}">
              
                <button type="submit" class="btn btn-success"><i class="fa fa-save fa-2x"></i> Registrar</button>
            
            </div>

            </div>

         </form>

    </div><!--fin del div card body-->
  </main>
  

@push('scripts')
 <script>
     
  $(document).ready(function(){
     //asigna la funcion agregar al boton con id #agregar
     $("#agregar").click(function(){

         agregar();
     });

  });

   var cont=0;//
   total=0;
   subtotal=[];//array para el subtotal que tendra los detalles que se vayan agregando
   $("#guardar").hide();//ocultamos el boton agregar para evitar enviar campos no asignados
//funcion para gregar
     function agregar(){

          id_producto= $("#id_producto").val();//tomamos el id producto del campo #id_producto
          producto= $("#id_producto option:selected").text();
          cantidad= $("#cantidad").val();
          precio_compra= $("#precio_compra").val();
          impuesto=20;//impuesto a calcular
        
          //si el id producto la cantidad y el precio son diferentes de vacio agrgar la compra
          if(id_producto !="" && cantidad!="" && cantidad>0 && precio_compra!=""){
            
             subtotal[cont]=cantidad*precio_compra;//calcular el subtotal
             total= total+subtotal[cont];
             //asignamos a la variable fila todos los detalles de la compra a una fila tr para ir mostrando los detalles de la compra junto con el subtotal, y se enumera con el contador, tambien tiene un boton para eliminar que manda a llamar a la funcion eliminar . se concatena el nombre del producto y su codigo
             var fila= '<tr class="selected" id="fila'+cont+'"><td><button type="button" class="btn btn-danger btn-sm" onclick="eliminar('+cont+');"><i class="fa fa-times fa-2x"></i></button></td> <td><input type="hidden" name="id_producto[]" value="'+id_producto+'">'+producto+'</td> <td><input type="number" id="precio_compra[]" name="precio_compra[]"  value="'+precio_compra+'"> </td>  <td><input type="number" name="cantidad[]" value="'+cantidad+'"> </td> <td>$'+subtotal[cont]+' </td></tr>';
             cont++;//el contador se suma con cada fila
             limpiar();//desoues de limpian los campos de cantidad y compra
             totales();//se calculan y actualizan los totales
            
             evaluar();//evaluamnos si el boton para guardar la compra debe aparecer
             $('#detalles').append(fila);//agregamos la fila 
            
            }else{

               // alert("Rellene todos los campos del detalle de la compra, revise los datos del producto");
               
                Swal.fire({
                type: 'error',
                text: 'Rellene todos los campos del detalle de la compras',
              
                })
            
            }
         
     }

    //funcion para limpiar la cantidad y precio de compra al momento de agregar un producto
     function limpiar(){
        
        $("#cantidad").val("");
        $("#precio_compra").val("");
        

     }
//funcion para calcular el total de la compra 
     function totales(){
        //asignamos lo que es el total
        $("#total").html("Mxn$ " + total.toFixed(2));//tofixed convierte a 2 decimales

        total_impuesto=total*impuesto/100;//calcula el impuesto
        total_pagar=total+total_impuesto;
        //muestra los totales
        $("#total_impuesto").html("Mxn$ " + total_impuesto.toFixed(2));
        $("#total_pagar_html").html("Mxn$ " + total_pagar.toFixed(2));
        $("#total_pagar").val(total_pagar.toFixed(2));
        
     }


//funcion para mostrar el boton de guardado
     function evaluar(){
        //si el total es mayor a 0 por lo tanto hay venta que registrar entonces mostrar el boton
         if(total>0){

           $("#guardar").show();

         } else{//de lo conrrario, al eliminar un unico registro volver a ocultar el boton
              
           $("#guardar").hide();
         }
     }
//funcion para eli,imar un detalle de la compra 
     function eliminar(index){

        total=total-subtotal[index];//resta el total con el total eliminado
        total_impuesto= total*20/100;//calcula el impusto
        total_pagar_html = total + total_impuesto;//recalcula el total
       //mouestra los totales 
        $("#total").html("Mxn$" + total);
        $("#total_impuesto").html("Mxn$" + total_impuesto);
        $("#total_pagar_html").html("Mxn$" + total_pagar_html);
        $("#total_pagar").val(total_pagar_html.toFixed(2));
       
        $("#fila" + index).remove();
        evaluar();
     }

 </script>
@endpush

@endsection