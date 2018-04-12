<?php

namespace Tests;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function setUp()
    {
        parent::setUp();

        config([
            'auth.providers.ldapusers' => [
                'driver' => 'ldapeloquent',
                'model' => \App\User::class
            ]
        ]);
        config([
            'guards.web.provider' => 'ldapusers'
        ]);
    }

    protected function getPackageProviders($app)
    {
        return ['UoGSoE\LaravelLdap\LaravelLdapServiceProvider'];
    }
}
