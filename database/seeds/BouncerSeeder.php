<?php

use App\Models\Invite;
use App\Models\Mailbox;
use App\Models\Message;
use App\Models\Project;
use App\Models\Tag;
use App\Models\Team;
use App\Models\User;

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BouncerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->reset();
        $admin = \App\Models\User::create([
            'email'             => 'admin@app.com',
            'first_name'        => 'admin',
            'last_name'         => 'user',
            'password'          => 'test1234',
            'email_verified_at' => Carbon::now(),
        ]);

        Bouncer::assign('admin')->to($admin);
        $users = factory(\App\Models\User::class, 50)
            ->create()
            ->each(function ($user) {
                $user->ownedTeams()->save(factory(App\Models\Team::class)->make());

                if ($user->id < 10) {
                    $user->ownedTeams()->save(factory(App\Models\Team::class)->make());
                    $user->ownedTeams()->save(factory(App\Models\Team::class)->make());
                }
                // $user->projects()->save(factory(App\Models\Project::class, 2)->make());
                $user->update(['active_team_id' => $user->ownedTeams()->first()->id]);
                Bouncer::scope()->to($user->active_team_id);
                Bouncer::assign('customer')->to($user);
            });
        $customer = \App\Models\User::create([
            'email'             => 'customer@app.com',
            'first_name'        => 'Customer',
            'last_name'         => 'The frog',
            'password'          => 'test1234',
            'email_verified_at' => Carbon::now()
        ]);

        $teams = Team::get();
        $users = User::get();

        foreach ($users as $user) {
            $user->teams()->attach(
                $teams->where('owner_id', '<>', $user->id)->random(rand(1, 3))->pluck('id')->toArray()
            );
        }

        Bouncer::allow('admin')->everything();
        // Bouncer::allow('admin')
        Bouncer::allow('employee')->toOwn(User::class);
        // Bouncer::allow('employee')->to('')
        Bouncer::allow('customer')->to('create', Project::class);
        Bouncer::allow('customer')->to('manage', Invite::class);
        Bouncer::allow('customer')->toOwn(Project::class);
        Bouncer::allow('customer')->toOwn(Team::class);
        Bouncer::allow('customer')->toOwn(Tag::class);
        Bouncer::allow('customer')->to('manage', Tag::class);
        Bouncer::allow('customer')->to('view', Project::class);
        Bouncer::allow('customer')->to('view', Invite::class);
        Bouncer::allow('customer')->to('view', Mailbox::class);
        Bouncer::allow('customer')->to('view', Message::class);
        Bouncer::allow('customer')->to('view', User::class);
        Bouncer::allow('customer')->toOwn(Invite::class);
        Bouncer::allow('customer')->toOwn(Mailbox::class);
        $customerTeam = $customer->teams->first();
        $customerTeam->owner_id = $customer->id;
        $customerTeam->save();
        $customer = $customer->refresh();
        $m = Mailbox::create([
            'username'      => env('TESTER_ACCOUNT_USERNAME'),
            'password'      => env('TESTER_ACCOUNT_PASSWORD'),
            'host'          => env('TESTER_ACCOUNT_HOST'),
            'port'          => env('TESTER_ACCOUNT_PORT'),
            'encryption'    => 'ssl',
            'team_id'       => $customer->ownedTeams()->first()->id ?? null,
            'user_id'       => $customer->id,
            'validate_cert' => false
        ]);
        $customer->active_team_id = $customer->ownedTeams->first()->id;
        $customer->save();
    }

    public function reset()
    {
        $tables = ['users', 'password_resets', 'settings', 'failed_jobs', 'mailboxes', 'media', 'messages', 'notifications', 'abilities', 'roles', 'assigned_roles', 'permissions', 'teams', 'team_users', 'invites', 'inviteables', 'projects', 'tags'];

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
    }
}
