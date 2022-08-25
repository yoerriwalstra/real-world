<?php

namespace Tests\Feature;

use App\Models\User;
use App\Rules\UpdateEmail;
use Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// class that can be used as a stand-in for an inline function
class MyInvokable
{
    public function __invoke()
    {
        //
    }
}

class UpdateEmailTest extends TestCase
{
    use RefreshDatabase;

    public function testItCallsFailFunctionWhenEmailIsTaken()
    {
        // setup
        $users = User::factory()->createMany([
            ['email' => 'email1@example.com'],
            ['email' => 'email2@example.com'],
        ]);

        Auth::shouldReceive('user')
            ->andReturn($users->first())
            ->shouldReceive('id')
            ->andReturn($users->first()->id);

        $mockFailInvokable = $this->createMock(MyInvokable::class);

        // expectation
        $mockFailInvokable
            ->expects($this->once())
            ->method('__invoke')
            ->with('The email address is already taken');

        // execution
        $rule = new UpdateEmail();
        $rule('email', 'email2@example.com', $mockFailInvokable);
    }
}
