<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'color',
        'status',
        'created_by',
    ];

    // ---------- Relationships ----------

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class)->orderBy('sort_order');
    }

    public function records(): HasMany
    {
        return $this->hasMany(Record::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(ProjectFile::class)->orderBy('sort_order');
    }

    // ---------- Scopes ----------

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
