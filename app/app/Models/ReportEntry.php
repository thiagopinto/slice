<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'report_file_id', 'report_type', 'entry_level',
        'category', 'subcategory', 'dimension_1', 'dimension_2',
        'count', 'credit_amount', 'debit_amount', 'total_amount',
        'currency', 'report_date', 'proc_date', 'raw_reference', 'raw_line'
    ];

    protected $casts = [
        'raw_line' => 'array',
        'report_date' => 'date',
        'proc_date' => 'date',
    ];

    public function reportFile(): BelongsTo
    {
        return $this->belongsTo(ReportFile::class);
    }
}
