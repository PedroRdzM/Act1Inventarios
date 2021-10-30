@extends('principal')
@section('contenido')


<main class="main">

 <div class="card-body">

 <h2>Modulo de Venta</h2>

 <span><strong>(*) Campo obligatorio</strong></span><br/>

 <h3 class="text-center">Ingresa los datos de la venta</h3>
    <!--llamada del metodo store-->
    <form action="{{route('venta.store')}}" method="POST">
    {{csrf_field()}}

            <div class="form-group row">

            <div class="col-md-8">  

                <label class="form-control-label" for="nombre">Nombre del Cliente</label>
                    <!--listado de cliemtes, se toma su id-->
                    <select class="form-control selectpicker" name="id_cliente" id="id_cliente" data-live-search="true" required>
                                                    
                    <option value="0" disabled>Seleccione</option>
                    
                    @foreach($clientes as $client)
                    
                    <option value="{{$client->id}}">{{$client->nombre}}</option>
                            
                    @endforeach

                    </select>
                
                </div>
            </div>

            <div class="form-group row">
                    <!--seccion para seleccionar el tipo de documento-->
                <div class="col-md-8">  

                        <label class="form-control-label" for="documento">Documento</label>
                        
                        <select class="form-control" name="tipo_identificacion" id="tipo_identificacion" required>
                                                        
                            <option value="0" disabled>Seleccione</option>
                            <option value="FACTURA">Factura</option>
                            <option value="TICKET">Ticket</option>
                            

                        </select>
                </div>
            </div>

            <!--seccion para agregar el numero de la venta-->
            <div class="form-group row">

                <div class="col-md-8">
                        <label class="form-control-label" for="num_venta">Número Venta</label>
                        
                        <input type="text" id="num_venta" name="num_venta" class="form-control" placeholder="Ingrese el número venta" pattern="[0-9]{0,15}">
                </div>
            </div>

            <br/><br/>
                        <!--seccion para escoger el producto-->
            <div class="form-group row border">

                 <div class="col-md-8">  

                        <label class="form-control-label" for="nombre">Producto</label>

                            <select class="form-control selectpicker" name="id_producto" id="id_producto" data-live-search="true" required>
                                                            
                            <option value="0" selected>Seleccione</option>
                            <!--ciclo para listar los productos -->
                            @foreach($productos as $prod)
                                <!--los productos son enviados  del metodo create mustra el codigo y el nombre del producto concatenados-->
                            <option value="{{$prod->id}}_{{$prod->stock}}_{{$prod->precio_venta}}">{{$prod->producto}}</option>
                                    
                            @endforeach<!--fin del ciclo-->

                            </select>

                </div>

            </div>

            <div class="form-group row">

                <div class="col-md-2">
                        <label class="form-control-label" for="cantidad">Cantidad</label>
                        
                        <input type="number" id="cantidad" name="cantidad" class="form-control" placeholder="Ingrese cantidad" pattern="[0-9]{0,15}">
                </div>

                <div class="col-md-2">
                        <label class="form-control-label" for="stock">Stock</label>
                        
                        <input type="number" disabled id="stock" name="stock" class="form-control" placeholder="Ingrese el stock" pattern="[0-9]{0,15}">
                </div>

                <div class="col-md-2">
                        <label class="form-control-label" for="precio_venta">Precio Venta</label>
                        
                        <input type="number" disabled id="precio_venta" name="precio_venta" class="form-control" placeholder="Ingrese precio de venta" >
                </div>

                <div class="col-md-2">
                        <label class="form-control-label" for="impuesto">Descuento</label>
                        
                        <input type="number" id="descuento" name="descuento" class="form-control" placeholder="Ingrese el descuento">
                </div>

                <div class="col-md-4">
                        
                    <button type="button" id="agregar" class="btn btn-primary"><i class="fa fa-plus fa-2x"></i> Agregar detalle</button>
                </div>


            </div>

            <br/><br/>

           <div class="form-group row border">

              <h3>Lista de Ventas a Clientes</h3>

              <div class="table-responsive col-md-12">
                <table id="detalles" class="table table-bordered table-striped table-sm">
                <thead>
                    <tr class="bg-success">
                        <th>Eliminar</th>
                        <th>Producto</th>
                        <th>Precio Venta (Mxn$)</th>
                        <th>Descuento</th>
                        <th>Cantidad</th>
                        <th>SubTotal (Mxn$)</th>
                    </tr>
                </thead>
                 
                <tfoot>
                   

                    <tr>
                        <th  colspan="5"><p align="right">TOTAL:</p></th>
                        <th><p align="right"><span id="total">Mxn$ 0.00</span> </p></th>
                    </tr>

                    <tr>
                        <th colspan="5"><p align="right">TOTAL IMPUESTO (20%):</p></th>
                        <th><p align="right"><span id="total_impuesto">MXn$ 0.00</span></p></th>
                    </tr>

                    <tr>
                        <th  colspan="5"><p align="right">TOTAL PAGAR:</p></th>
                        <th><p align="right"><span align="right" id="total_pagar_html">Mxn$ 0.00</span> <input type="hidden" name="total_pagar" id="total_pagar"></p></th>
                    </tr>  

                </tfoot>

                <tbody>
                </tbody>
                
                
                </table>
              </div>
            
            </div>

            <div class="modal-footer form-group row" id="guardar">
            
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
     //llama a la funcion agregar
     $("#agregar").click(function(){

         agregar();
     });

  });

   var cont=0;//contador para las filas de los producttos a agregar
   total=0;
   subtotal=[];
   $("#guardar").hide();//se oculta el boton de guardar para evitar que se guarde una compra sin productos
   $("#id_producto").change(mostrarValores);//al elegir un producto cambia los valores con la funcion(mostrarValores) deacuerdo al id del producto

     function mostrarValores(){

         datosProducto = document.getElementById('id_producto').value.split('_');
         $("#precio_venta").val(datosProducto[2]);//muestra informacion de la base de datos del precio
         $("#stock").val(datosProducto[1]);//muestra el stock disponible del producto
     
     }

     function agregar(){//funcion para agregar un producto al detalle
        //toma los datos del formulario
         datosProducto = document.getElementById('id_producto').value.split('_');

         id_producto= datosProducto[0];
         producto= $("#id_producto option:selected").text();
         cantidad= $("#cantidad").val();
         descuento= $("#descuento").val();
         precio_venta= $("#precio_venta").val();
         stock= $("#stock").val();
         impuesto=20;
          //si el el id, la cantidad  y descuento son distintos a "" o vacios 
          if(id_producto !="" && cantidad!="" && cantidad>0  && descuento!="" && precio_venta!=""){
                //obtenemos el stock y compara si el stock es mayor a cantidad se agrega la fila
                if(parseInt(stock)>=parseInt(cantidad)){
                    
                    subtotal[cont]=(cantidad*precio_venta)-(cantidad*precio_venta*descuento/100);
                    total= total+subtotal[cont];//calcula el subtotal del producto
                    //se agrega la fila con el id del producto y nombre, el precio,cantidad,descuento y subtotal
                    var fila= '<tr class="selected" id="fila'+cont+'"><td><button type="button" class="btn btn-danger btn-sm" onclick="eliminar('+cont+');"><i class="fa fa-times fa-2x"></i></button></td> <td><input type="hidden" name="id_producto[]" value="'+id_producto+'">'+producto+'</td> <td><input type="number" name="precio_venta[]" value="'+parseFloat(precio_venta).toFixed(2)+'"> </td> <td><input type="number" name="descuento[]" value="'+parseFloat(descuento).toFixed(2)+'"> </td> <td><input type="number" name="cantidad[]" value="'+cantidad+'"> </td> <td>$'+parseFloat(subtotal[cont]).toFixed(2)+'</td></tr>';
                    cont++;
                    limpiar();//llama al metodo limpiar
                    totales();  //llama al metodo totales
                    evaluar(); //llama a evaluar
                    $('#detalles').append(fila);//agrega la fila dentro del id detalles #detalles

                } else{


                //si la cantidad  supera el stock se muestra una alerta
                    Swal.fire({
                    type: 'error',
                    
                    text: 'La cantidad a vender supera el stock',
                
                    })
                }
               
            }else{

           //si hay campos vacios, muestra una alerta
           
                Swal.fire({
                type: 'error',
              
                text: 'Rellene todos los campos del detalle de la venta',
              
                })
           
            }
         
     }

      //limpia el formulario para agregar un detalle del producto
     function limpiar(){
        
        $("#cantidad").val("");
        $("#descuento").val("0");
        $("#precio_venta").val("");

     }

     function totales(){

        $("#total").html("Mnx$ " + total.toFixed(2));//se agrega lo que se tiene en la variable total a #total
    

        total_impuesto=total*impuesto/100;//calcula el impuesto
        total_pagar=total+total_impuesto;//calrula el total
        $("#total_impuesto").html("Mnx$ " + total_impuesto.toFixed(2));//asigna los valores a los campos en el <span>
        $("#total_pagar_html").html("Mxn$ " + total_pagar.toFixed(2));
        $("#total_pagar").val(total_pagar.toFixed(2));
      }


     function evaluar(){//revisa si el boton de guardar se muestra en el formulario

         if(total>0){//si el total a pagar es mayor a 0 entonces se muestra el boton

           $("#guardar").show();

         } else{//en caso que el total sea menor a 0 se oculta el boton de guardar
              
           $("#guardar").hide();
         }
     }

     function eliminar(index){//funcion para eliminaruna fila del detalle de la compra

        total=total-subtotal[index];
        total_impuesto= total*20/100;
        total_pagar_html = total + total_impuesto;

        $("#total").html("Mxn$" + total);
        $("#total_impuesto").html("Mxn$" + total_impuesto);
        $("#total_pagar_html").html("Mxn$" + total_pagar_html);
        $("#total_pagar").val(total_pagar_html.toFixed(2));
        
        $("#fila" + index).remove();//elimina la fila
        evaluar();
     }

 </script>
@endpush

@endsection