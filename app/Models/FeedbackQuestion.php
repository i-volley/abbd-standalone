<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedbackQuestion extends Model
{
    protected $table = 'feedback_questions';

    protected $fillable = ['paradigm', 'question_text', 'question_type', 'is_active', 'position'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Domande per un paradigma specifico:
     * restituisce 'both' + quelle del paradigma dato.
     */
    public function scopeForParadigm($query, string $paradigm)
    {
        return $query->whereIn('paradigm', ['both', $paradigm]);
    }
}
