<!-- Extencion de la plantilla principal -->
@extends('principal')
@section('contenido')
<main class="main">
            <!-- Breadcrumb -->
            <ol class="breadcrumb">
                <li class="breadcrumb-item active"><a href="/">SISTEMA DE COMPRAS - VENTAS</a></li>
            </ol>
            <div class="container-fluid">
                <!-- Ejemplo de tabla Listado -->
                <div class="card">
                    <div class="card-header">

                       <h2>Listado de Compras</h2><br/>
                       <!-- redirige a la plantilla de create -->
                       <a href="compra/create">

                        <button class="btn btn-primary btn-lg" type="button">
                            <i class="fa fa-plus fa-2x"></i>&nbsp;&nbsp;Agregar Compra
                        </button>

                        </a>
                       
                    </div>
                    <div class="card-body">

                        <div class="form-group row">
                            <div class="col-md-6">
                            {!! Form::open(array('url'=>'compra','method'=>'GET','autocomplete'=>'off','role'=>'search')) !!} 
                                <div class="input-group">
                                   <!-- barra para el buscador -->
                                    <input type="text" name="buscarTexto" class="form-control" placeholder="Buscar texto" value="{{$buscarTexto}}">
                                    <button type="submit"  class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            {{Form::close()}}
                            </div>
                        </div>
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr class="bg-primary">
                                    <!-- titulos de la tabla -->
                                    <th>Ver Detalle</th>
                                    <th>Fecha Compra</th>
                                    <th>Número Compra</th>
                                    <th>Proveedor</th>
                                    <th>Tipo de identificación</th>
                                    <th>Comprador</th> 
                                    <th>Total (Mxn$)</th>
                                    <th>Impuesto</th>
                                    <th>Estado</th>
                                    <th>Cambiar Estado</th>
                                    <th>Descargar Reporte</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Breadcrumla variable compras tiene los regoistros y se almacena en comp -->
                              @foreach($compras as $comp)
                               
                                <tr>
                                    <td>
                                     <!-- reedirecciona al metodo show situado en comptacontroller y vemos el detalle -->
                                     <a href="{{URL::action('App\Http\Controllers\CompraController@show',$comp->id)}}">
                                       <button type="button" class="btn btn-warning btn-md">
                                         <i class="fa fa-eye fa-2x"></i> Ver detalle
                                       </button> &nbsp;

                                     </a>
                                   </td>

                                    <td>{{$comp->fecha_compra}}</td>
                                    <td>{{$comp->num_compra}}</td>
                                    <td>{{$comp->proveedor}}</td>
                                    <td>{{$comp->tipo_identificacion}}</td>
                                    <td>{{$comp->nombre}}</td>
                                    <td>${{number_format($comp->total,2)}}</td><!-- number_format para  convertir a 2 decimales -->
                                    <td>{{$comp->impuesto}}</td>
                                    <td>
                                      <!-- si el estado es igual a registrado aparece el boton registrado, si nno, aparecera anulado-->
                                      @if($comp->estado=="Registrado")
                                        <button type="button" class="btn btn-success btn-md">
                                    
                                          <i class="fa fa-check fa-2x"></i> Registrado
                                        </button>

                                      @else

                                        <button type="button" class="btn btn-danger btn-md">
                                    
                                          <i class="fa fa-check fa-2x"></i> Anulado
                                        </button>

                                       @endif
                                       
                                    </td>

                                    
                                    <td>

                            
                                            <!-- si el estado es registado podemos anular la compra -->
                                            @if($comp->estado=="Registrado")
                                                                                        <!-- se llama al id de la compra para abrir la ventana de confirmacion -->
                                                <button type="button" class="btn btn-danger btn-sm" data-id_compra="{{$comp->id}}" data-toggle="modal" data-target="#cambiarEstadoCompra">
                                                    <i class="fa fa-times fa-2x"></i> Anular Compra
                                                </button>

                                                @else

                                                <button type="button" class="btn btn-success btn-sm">
                                                    <i class="fa fa-lock fa-2x"></i> Anulado
                                                </button>

                                            @endif
                                       
                                    </td>

                                    <td>
                                       
                                       <a href="{{url('pdfCompra',$comp->id)}}" target="_blank">
                                          
                                          <button type="button" class="btn btn-info btn-sm">
                                           
                                            <i class="fa fa-file fa-2x"></i> Descargar PDF
                                          </button> &nbsp;

                                       </a> 

                                   </td>
                                </tr>

                                @endforeach
                               
                            </tbody>
                        </table>

                        {{$compras->render()}}
                        
                    </div>
                </div>
                <!-- Fin ejemplo de tabla Listado -->
            </div>
                       
           
        <!-- Inicio del modal cambiar estado de compra -->
         <div class="modal fade" id="cambiarEstadoCompra" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-danger" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Cambiar Estado de Compra</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <!-- llama al metodo destroy en compra controller para anular la compra -->
                    <div class="modal-body">
                        <form action="{{route('compra.destroy','test')}}" method="POST">
                          {{method_field('delete')}}
                          {{csrf_field()}} 

                            <input type="hidden" id="id_compra" name="id_compra" value="">

                                <p>Estas seguro de cambiar el estado?</p>
        

                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times fa-2x"></i>Cerrar</button>
                                <button type="submit" class="btn btn-success"><i class="fa fa-lock fa-2x"></i>Aceptar</button>
                            </div>

                         </form>
                    </div>
                    <!-- /.modal-content -->
                   </div>
                <!-- /.modal-dialog -->
             </div>
            <!-- Fin del modal Eliminar -->
           
            
        </main>
@endsection