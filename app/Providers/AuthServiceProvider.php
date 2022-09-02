<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('follow-profile', fn (User $user, string $username) => $user->username !== $username);
        Gate::define(
            'update-article',
            fn (User $user, string $slug) => $user->id === Article::query()->where('slug', $slug)->first()?->author_id
        );
        Gate::define(
            'delete-comment',
            fn (User $user, string $id) => $user->id === Comment::query()->where('id', $id)->first()?->author_id
        );
    }
}
