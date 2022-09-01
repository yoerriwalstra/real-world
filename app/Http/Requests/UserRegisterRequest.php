<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'user.username' => 'required|string|alpha_num|min:3|max:255|unique:users,username',
            'user.email' => 'required|email|max:255|unique:users,email',
            'user.password' => 'required|string|min:8|max:255', // TODO: optionally add more specific password requirements
        ];
    }
}
