<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pagos extends Model
{
    protected $table = 'pagos';
    protected $fillable = ['user_id', 'evento_id', 'amount', 'currency', 'payment_method', 'status', 'transaction_id', 'paid_at'];
}
