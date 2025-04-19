<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Vss110ResumoLiquidacao extends Model
{
    use HasFactory;

    protected $fillable = [
        'relatorio_id',
        'categoria',
        'tipo_transacao',
        'quantidade',
        'valor',
    ];

    public function relatorio(): BelongsTo
    {
        return $this->belongsTo(Relatorio::class, 'relatorio_id');
    }
}
