<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('atleta');
    }

    public function rules(): array
    {
        return [
            'seduta_id'                    => 'required|exists:sedute,id',
            'rpe'                          => 'required|integer|min:1|max:10',
            'qualita_prestazione'          => 'required|integer|min:1|max:10',
            'impegno_squadra'              => 'required|integer|min:1|max:10',
            'miglioramento_fondamentale'   => 'required|integer|min:1|max:5',
            'nota'                         => 'nullable|string|max:500',
            'gradimento_esercizio'         => 'nullable|array',
            'gradimento_esercizio.*'       => 'integer|min:1|max:5',
        ];
    }
}
