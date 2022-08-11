<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'user.username' => 'required|string|min:3',
            'user.email' => 'required|email|unique:users,email',
            'user.password' => 'required|string|min:8', // TODO: optionally add more specific password requirements
        ];
    }
}