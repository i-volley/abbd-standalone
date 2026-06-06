<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionTemplateBlock extends Model
{
    public $timestamps = false;

    protected $table = 'session_template_blocks';

    protected $fillable = [
        'session_template_id', 'position', 'block_name', 'block_description',
        'suggested_duration_minutes', 'block_type', 'constraint_focus',
    ];

    public function template()
    {
        return $this->belongsTo(SessionTemplate::class, 'session_template_id');
    }

    /** Etichette leggibili per block_type in base al paradigma */
    public static function blockTypeLabel(string $blockType, string $paradigm = 'traditional'): string
    {
        return match($blockType) {
            'warmup'                => $paradigm === 'ecological' ? 'Attivazione percettivo-motoria' : 'Riscaldamento',
            'technical'             => 'Tecnica',
            'tactical'              => 'Tattica',
            'ecological_constraint' => 'Vincolo ecologico',
            'game_form'             => $paradigm === 'ecological' ? 'Forma di gioco RLD' : 'Forma di gioco',
            'cooldown'              => $paradigm === 'ecological' ? 'Riflessione guidata' : 'Cool-down',
            'free'                  => 'Blocco libero',
            default                 => $blockType,
        };
    }

    /** Colore badge Bootstrap per block_type */
    public static function blockTypeColor(string $blockType): string
    {
        return match($blockType) {
            'warmup'                => 'warning',
            'technical'             => 'primary',
            'tactical'              => 'info',
            'ecological_constraint' => 'success',
            'game_form'             => 'danger',
            'cooldown'              => 'secondary',
            'free'                  => 'dark',
            default                 => 'light',
        };
    }
}
