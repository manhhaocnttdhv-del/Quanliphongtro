<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'phone'          => ['nullable', 'string', 'max:20'],
            'id_card'        => ['nullable', 'string', 'max:20'],
            'dob'            => ['nullable', 'date'],
            'gender'         => ['nullable', 'in:male,female,other'],
            'province_name'  => ['nullable', 'string', 'max:100'],
            'district_name'  => ['nullable', 'string', 'max:100'],
            'ward_name'      => ['nullable', 'string', 'max:100'],
            'address_detail' => ['nullable', 'string', 'max:255'],
        ];
    }
}
