<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorageConnectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'key' => 'required|string|max:255',
            'secret' => 'required|string|max:60000',
            'region' => 'required|string|max:20',
            'bucket' => 'required|string|max:100',
            'endpoint' => 'nullable|url|max:255',
            'use_path_style' => 'nullable',
        ];
    }
}
