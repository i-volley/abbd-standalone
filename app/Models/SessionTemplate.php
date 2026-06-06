<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionTemplate extends Model
{
    protected $table = 'session_templates';

    protected $fillable = ['name', 'paradigm', 'description', 'is_system', 'created_by'];

    protected function casts(): array
    {
        return ['is_system' => 'boolean'];
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeForParadigm($query, string $paradigm)
    {
        return $query->where('paradigm', $paradigm);
    }

    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    // ── Relations ─────────────────────────────────────────────────────────────

    public function blocks()
    {
        return $this->hasMany(SessionTemplateBlock::class)->orderBy('position');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
