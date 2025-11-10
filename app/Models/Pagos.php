<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pagos extends Model
{
    protected $table = 'pagos';
    protected $fillable = ['user_id', 'evento_id', 'amount', 'currency', 'payment_method', 'status', 'transaction_id', 'paid_at'];
    
    /**
     * Obtiene el usuario que realizó el pago.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
    
    /**
     * Obtiene el evento relacionado con el pago.
     */
    public function evento()
    {
        return $this->belongsTo('App\Models\Eventos', 'evento_id');
    }
}
