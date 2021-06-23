<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    /**
 * Get the element shortcuts for the page.
 *
 * @return array
 */
    public function elements()
    {
        return [
            '@email'      => '#emailInput',
            '@pw'         => '#passwordInput',
            '@pw_confirm' => '#passwordConfirmationInput',
            '@submit'     => '#formSubmit'
        ];
    }

    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertPathIs('/login')
                ->assertSee('Welcome Back!')
                // ->assertVisible('@submit')
                ->type('#emailInput', 'admin@app.com')
                ->type('#passwordInput', 'test1234')
                ->press('Login')
                ->waitForLocation('/')
                ->assertSee('Dashboard')
                ;
            // $browser->type('email', 'admin@app.com');
            // $browser->type('password', 'test1234');
            // $browser->clickLink('Login');
            // $this->assertSee('Dashboard');
        });
    }
}
