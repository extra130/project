<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecordFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'record_id',
        'original_name',
        'display_name',
        'note',
        'storage_path',
        'mime_type',
        'extension',
        'file_size',
        'sort_order',
        'uploaded_by',
    ];

    // ---------- Relationships ----------

    public function record(): BelongsTo
    {
        return $this->belongsTo(Record::class);
    }
}
