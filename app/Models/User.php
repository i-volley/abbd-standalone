<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name', 'email', 'password',
        'paradigm', 'paradigm_weight_ecological',
        'feedback_style', 'ai_suggestion_tone', 'preferred_session_blocks',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ── Paradigm accessors ────────────────────────────────────────────────────

    public function getParadigmLabelAttribute(): string
    {
        return match($this->paradigm ?? 'traditional') {
            'traditional' => 'Tradizionale',
            'ecological'  => 'Ecologico',
            'hybrid'      => 'Ibrido',
            default       => 'Tradizionale',
        };
    }

    public function getEcologicalWeightPercentAttribute(): int
    {
        return (int) ($this->paradigm_weight_ecological ?? 0);
    }

    public function getActiveFeedbackQuestions()
    {
        return FeedbackQuestion::active()
            ->forParadigm($this->paradigm ?? 'traditional')
            ->orderBy('position')
            ->get();
    }

    public function getPreferredSessionTemplate()
    {
        return SessionTemplate::system()
            ->forParadigm($this->paradigm ?? 'traditional')
            ->with('blocks')
            ->first();
    }

    // ── Relations ─────────────────────────────────────────────────────────────

    public function feedback()
    {
        return $this->hasMany(Feedback::class, 'atleta_id');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_atleta', 'user_id', 'team_id');
    }

    public function teamsAllenati()
    {
        return $this->hasMany(Team::class, 'allenatore_id');
    }
}
