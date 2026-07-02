<?php

namespace App\Domains\Preferences\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePreferenceRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Authorizado via middleware auth
    }

    public function rules()
    {
        return [
            'settings' => 'required|array',
        ];
    }
}
