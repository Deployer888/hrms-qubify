<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndustryListRequest extends FormRequest
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
        $id = $this->route('industry_list')->id;
        // dd($id);
        if($id){
            $iconValidate = 'file|mimes:svg,png';
        }else{
            $iconValidate = 'required|file|mimes:svg,png';
        }
        return [
			'industry_id' => 'required|string',
			'name' => 'required|string',
			'icon' => $iconValidate,
			'description' => 'required|string',
        ];
    }
}
