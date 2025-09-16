<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrustedLogoRequest extends FormRequest
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
        $industryId = $this->route('id');
        if($industryId){
            $iconValidate = 'file|mimes:svg,png';
        }else{
            $iconValidate = 'required|file|mimes:svg,png';
        }
        $rules = [
            'image' => $iconValidate,
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'image.required' => 'The image field is required.',
            'image.file' => 'The image must be a file.',
            'image.mimes' => 'Only SVG, PNG files are allowed for the slider image.',
        ];
    }
}
