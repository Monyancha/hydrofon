<?php

namespace Tests\Browser\Impersonation;

use Hydrofon\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ImpersonationTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * An administrator can impersonate a user.
     *
     * @return void
     */
    public function testAdminCanImpersonateUser()
    {
        $admin = factory(User::class)->states('admin')->create();
        $user = factory(User::class)->create();

        $this->browse(function (Browser $browser) use ($admin, $user) {
            $browser->loginAs($admin)
                    ->visit('/home')
                    // See impersonation form.
                    ->assertSourceHas('topbar-impersonation')
                    ->select('user_id', $user->id)

                    // User is impersonated.
                    ->assertPathIs('/home')
                    ->assertDontSeeIn('.sidebar', $admin->name)
                    ->assertSeeIn('.sidebar', $user->name)
                    ->assertSeeLink('Stop impersonation')

                    // Stop impersonating, recoqnized as administrator user again.
                    ->clickLink('Stop impersonation')
                    ->assertPathIs('/home')
                    ->assertDontSeeIn('.sidebar', $user->name)
                    ->assertSeeIn('.sidebar', $admin->name)
                    ->assertDontSeeLink('Stop impersonation');
        });
    }

    /**
     * A user can not see impersonation form.
     *
     * @return void
     */
    public function testUserCanNotSeeImpersonationForm()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $this->browse(function (Browser $browser) use ($user1, $user2) {
            $browser->loginAs($user1)
                    ->visit('/home')
                    ->assertSourceMissing('topbar-impersonation');
        });
    }
}
