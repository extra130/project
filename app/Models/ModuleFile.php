<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'original_name',
        'storage_path',
        'mime_type',
        'extension',
        'file_size',
        'uploaded_by',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
