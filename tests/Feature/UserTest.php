<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testItHashesThePasswordWithAMutator()
    {
        $user = new User();
        $password = 'password';

        $user->password = $password;

        $this->assertTrue(Hash::check($password, $user->password));
    }
}
