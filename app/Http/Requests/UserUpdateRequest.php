<?php

namespace App\Http\Requests;

use App\Rules\UpdateEmail;
use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'user.username' => 'string|min:3|max:255',
            'user.email' => ['string', 'email', 'max:255', new UpdateEmail],
            'user.password' => 'string|min:8|max:255',
            'user.bio' => 'nullable|string',
            'user.image' => 'nullable|file|image|size:2048',
        ];
    }
}
