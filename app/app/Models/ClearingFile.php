<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClearingFile extends Model
{
    protected $fillable = [
        'data_processamento',
        'data_relatorio',
        'moeda_liquidacao',
        'entidade_fundo_transferencia',
        'arquivo_origem',
        'arquivo_path',
    ];
}
