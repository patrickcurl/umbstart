<?php

declare(strict_types=1);
namespace Tests;

use Drfraker\SnipeMigrations\SnipeMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Assert;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, SnipeMigrations;

    protected static $migrationsRun = false;

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function signIn($user = null)
    {
        $user = $user ?: create('App\User');

        $this->actingAs($user);

        return $this;
    }

    protected function signInAdmin

    public function logResponse($response) : void
    {
        try {
            \Log::info($this->getResponseData($response));
        } catch (\Exception $e) {
            \Log::info($e->getMessage() . ' on: ' . $e->getLine() . ' in: ' . $e->getFile());
        }
    }

    public function getResponseData($response)
    {
        try {
            $data       = json_decode($response->content(), true);
        } catch (\Exception $e) {
            $data = [];
        }

        return $data;
    }

    protected function signInViaPassport($user = null)
    {
        $this->clearRedisCache();

        $user = $user ?: create('App\User');

        Passport::actingAs($user);

        return $this;
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('cache.default', 'array');
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }
}
