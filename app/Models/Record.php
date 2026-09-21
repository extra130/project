<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Record extends Model
{
    use HasFactory;

    // Valid type values per spec §9
    const TYPES = ['development', 'test', 'issue', 'note'];

    // Valid source values per spec §10
    const SOURCES = ['manual', 'codex', 'agent', 'api'];

    protected $fillable = [
        'project_id',
        'module_id',
        'type',
        'title',
        'content',
        'source',
        'git_branch',
        'git_commit',
        'created_by',
    ];

    // ---------- Relationships ----------

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(RecordFile::class)->orderBy('sort_order');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
