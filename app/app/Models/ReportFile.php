<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'report_source_id', 'report_type', 'source_file', 'emitter_name',
        'emitter_id', 'currency', 'report_date', 'proc_date'
    ];

    public function entries(): HasMany
    {
        return $this->hasMany(ReportEntry::class);
    }

    public function reportSource()
    {
        return $this->belongsTo(ReportSource::class);
    }
}
