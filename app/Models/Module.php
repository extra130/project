<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'description',
        'sort_order',
        'status',
    ];

    // ---------- Relationships ----------

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function records(): HasMany
    {
        return $this->hasMany(Record::class);
    }

    // ---------- Scopes ----------

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
