<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\InvokableRule;

class UpdateEmail implements InvokableRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {
        if (auth()->user()->email !== $value && $this->emailIsTaken($value)) {
            $fail('The email address is already taken');
        }
    }

    private function emailIsTaken(string $value)
    {
        return User::where('email', $value)->where('id', '!=', auth()->id())->exists();
    }
}
