<?php

namespace Tests\Feature;

use Illuminate\Auth\AuthenticationException;
use Tests\TestCase;

class RequiresAuthenticationTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testItThrowsAuthenticationException(string $method, string $url, array $data = [])
    {
        $this->withoutExceptionHandling();

        $this->expectException(AuthenticationException::class);

        $this->$method("/api/$url", $data);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testItReturnsUnauthorizedMessage(string $method, string $url, array $data = [], string $message = 'Unauthenticated.')
    {
        $response = $this->$method("/api/$url", $data);

        $response->assertUnauthorized();
        $response->assertJson(['message' => $message]);
    }

    public function dataProvider()
    {
        return [
            ['postJson', 'articles'],
            ['putJson', 'articles/easy-title'],
            ['deleteJson', 'articles/easy-title'],
            ['postJson', 'articles/easy-title/favorite'],
            ['deleteJson', 'articles/easy-title/favorite'],
            ['postJson', 'articles/easy-title/comments'],
            ['deleteJson', 'articles/easy-title/comments/1'],
            [
                'postJson',
                'users/login',
                [
                    'user' => [
                        'email' => 'non-existent@user.com',
                        'password' => 'password',
                    ],
                ],
                'Unauthorized',
            ],
        ];
    }
}
