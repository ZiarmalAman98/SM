<?php

namespace App\Filament\Resources\StudentResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
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
			'name' => 'required',
			'last_name' => 'required',
			'email' => 'required',
			'email_verified_at' => 'required',
			'password' => 'required',
			'type' => 'required',
			'remember_token' => 'required',
			'active_status' => 'required',
			'avatar' => 'required',
			'dark_mode' => 'required',
			'messenger_color' => 'required'
		];
    }
}
