<?php

namespace App\Filament\Resources\BonusResource\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBonusRequest extends FormRequest
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
			'user_id' => 'required',
			'amount' => 'required|numeric',
			'reason' => 'required|string',
			'date' => 'required|date'
		];
    }
}
