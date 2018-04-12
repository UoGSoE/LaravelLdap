<?php

namespace UoGSoE\LaravelLdap;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class LaravelLdapServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/ldap.php', 'auth');
        dd(config('auth'));
    }

    public function boot()
    {
        \Auth::provider('ldapeloquent', function ($app, array $config) {
            return new LdapLocalUserProvider($app['hash'], $config['model']);
        });
    }
}
