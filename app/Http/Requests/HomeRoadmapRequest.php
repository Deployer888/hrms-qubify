<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\HomeRoadmap;
use Illuminate\Validation\Rule;
class HomeRoadmapRequest extends FormRequest
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
        $homeRoadmapId = $this->route('home_roadmap');

        $iconValidate = $homeRoadmapId ? 'file|mimes:svg,png' : 'required|file|mimes:svg,png';

        $rules = [
            'title' => [
                'required',
                Rule::unique('home_roadmap', 'title')->ignore($homeRoadmapId),
            ],
            'description' => 'required',
            'icon' => $iconValidate,
        ];

        return $rules;

    }
}
