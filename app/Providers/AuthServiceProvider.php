<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Validator;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies=[];

    /**
     * The forbidden doamins that cannot register NOJ.
     *
     * @var array
     */
    protected $forbiddenDomains=['temporary.email'];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Passport::routes();

        Validator::extend('allowed_email_domain', function($attribute, $value, $parameters, $validator) {
            return !in_array(explode('@', $value)[1], $this->forbiddenDomains);
        }, 'Domain not valid for registration.');
    }
}
