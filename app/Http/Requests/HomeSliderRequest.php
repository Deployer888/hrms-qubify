<?php

namespace App\Http\Requests;
use Illuminate\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class HomeSliderRequest extends FormRequest
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
        $industryId = $this->route('home_slider');
        if($industryId){
            $iconValidate = 'file|mimes:svg,png,jpg,jpeg';
        }else{
            $iconValidate = 'required|file|mimes:svg,png,jpg,jpeg';
        }
        $rules = [
            'heading' => 'required|unique:home_sliders,heading',
            'sub_heading' => 'required',
            'image' => $iconValidate,
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'heading.required' => 'The name field is required.',
            'heading.unique' => 'The name has already been taken.',
            'sub_heading.required' => 'The description field is required.',
            'image.required' => 'The image field is required.',
            'image.file' => 'The image must be a file.',
            'image.mimes' => 'Only SVG, JPG, JPEG, PNG files are allowed for the slider image.',
        ];
    }
}
