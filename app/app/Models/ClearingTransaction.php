<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClearingTransaction extends Model
{
    protected $fillable = [
        'arn',
        'slice_code',
        'clearing_value',
        'clearing_currency',
        'clearing_commission',
        'issuer_exchange_rate',
        'operation_code',
    ];
}
