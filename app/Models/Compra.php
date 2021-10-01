<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $table = 'compras';
        
        protected $fillable = [
            'idproveedor',
            'idusuario',
            'tipo_identificacion',
            'num_compra',
            'fecha_compra',
            'impuesto',
            'total',
            'estado'
        ];

        /*es el usuario que hace el registro*/
     public function usuario()
     {
         return $this->belongsTo('App\Http\Models\User');
     }

     /*el proveedor que hace la compra*/
     public function proveedor()
     {
         return $this->belongsTo('App\Http\Models\Proveedor');
     }
}
