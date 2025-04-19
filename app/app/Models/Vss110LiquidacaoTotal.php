<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vss110LiquidacaoTotal extends Model
{
    use HasFactory;

    protected $fillable = [
        'relatorio_id',
        'descricao',
        'valor',
    ];

    public function relatorio(): BelongsTo
    {
        return $this->belongsTo(Relatorio::class, 'relatorio_id');
    }
}
