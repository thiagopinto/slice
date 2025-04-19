<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Relatorio extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo_relatorio',
        'data_processamento',
        'data_relatorio',
        'entidade_fundo_transferencia',
        'moeda_liquidacao',
        'arquivo_origem',
    ];

    public function vss110ResumosLiquidacao(): HasMany
    {
        return $this->hasMany(Vss110ResumoLiquidacao::class, 'relatorio_id');
    }

    public function vss110TransferenciasFundos(): HasMany
    {
        return $this->hasMany(Vss110TransferenciaFundo::class, 'relatorio_id');
    }

    public function vss110LiquidacoesTotais(): HasMany
    {
        return $this->hasMany(Vss110LiquidacaoTotal::class, 'relatorio_id');
    }

    public function vss115RecapsLiquidacao(): HasMany
    {
        return $this->hasMany(Vss115RecapLiquidacao::class, 'relatorio_id');
    }

    public function vss600AgendaFees(): HasMany
    {
        return $this->hasMany(Vss600AgendaFee::class, 'relatorio_id');
    }
}
