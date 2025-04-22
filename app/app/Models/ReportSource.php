<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'file_name', 'file_path', 'imported_at',
    ];

    public function reportFiles()
    {
        return $this->hasMany(ReportFile::class);
    }
}
