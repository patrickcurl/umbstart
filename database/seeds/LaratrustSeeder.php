<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class LaratrustSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return  void
     */
    public function run()
    {
        $this->command->info('Truncating User, Role and Permission tables');
        $this->truncateLaratrustTables();
        $config         = config('account.seeder.role_structure');
        $userPermission = config('account.seeder.permission_structure');
        $mapPermission  = collect(config('account.seeder.permissions_map'));

        foreach ($config as $key => $modules) {

            // Create a new role
            $role = \App\Account\Role::create([
                'name'         => $key,
                'display_name' => ucwords(str_replace('_', ' ', $key)),
                'description'  => ucwords(str_replace('_', ' ', $key))
            ]);
            $permissions = [];

            $this->command->info('Creating Role '. strtoupper($key));

            // Reading role permission modules
            foreach ($modules as $module => $value) {
                \Log::info(['module' => $module, 'value' => $value]);

                foreach (explode(',', $value) as $p => $perm) {
                    $permissionValue = $mapPermission->get($perm);

                    $permissions[] = \App\Account\Permission::firstOrCreate([
                        'name'         => $permissionValue . '-' . $module,
                        'display_name' => ucfirst($permissionValue) . ' ' . ucfirst($module),
                        'description'  => ucfirst($permissionValue) . ' ' . ucfirst($module),
                    ])->id;

                    $this->command->info('Creating Permission to '.$permissionValue.' for '. $module);
                }
            }

            // Attach all permissions to the role
            $role->permissions()->sync($permissions);

            $this->command->info("Creating '{$key}' user");

            // Create default user for each role

            $user = \App\Account\User::create([
                'first_name'     => 'John',
                'last_name'      => 'Doe',
                'email'          => $key.'@app.com',
                'password'       => 'test1234'
            ]);

            $user->attachRole($role);
        }

        // Creating user with permissions
        if (!empty($userPermission)) {
            foreach ($userPermission as $key => $modules) {
                foreach ($modules as $module => $value) {

                    // Create default user for each permission set
                    $user = \App\Account\User::create([
                        'first_name'     => 'John',
                        'last_name'      => 'Doe',
                        'email'          => $key.'@app.com',
                        'password'       => 'test1234',
                        'remember_token' => Str::random(10),
                    ]);
                    $permissions = [];

                    foreach (explode(',', $value) as $p => $perm) {
                        $permissionValue = $mapPermission->get($perm);

                        $permissions[] = \App\Account\Permission::firstOrCreate([
                            'name'         => $permissionValue . '-' . $module,
                            'display_name' => ucfirst($permissionValue) . ' ' . ucfirst($module),
                            'description'  => ucfirst($permissionValue) . ' ' . ucfirst($module),
                        ])->id;

                        $this->command->info('Creating Permission to '.$permissionValue.' for '. $module);
                    }
                }

                // Attach all permissions to the user
                $user->permissions()->sync($permissions);
            }
        }
        $u    = \App\Account\User::find(1);
        $team = $u->teams()->create(['name' => 'team1']);

        // $team = factory(\App\Account\Team::class, 5)->create(['owner_id', \App\Account\User::first()->id]);
        // $team->users()->sync(\App\Account\User::where('id', '<>', \App\Account\User::first()->id)->pluck('id'));
    }

    /**
     * Truncates all the laratrust tables and the users table
     *
     * @return    void
     */
    public function truncateLaratrustTables()
    {
        Schema::disableForeignKeyConstraints();
        $tables = [
            'role_permissions',
            'user_permissions',
            'user_roles',
            'users',
            'roles',
        ];

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
        // DB::table('role_permissions')->truncate();
        // DB::table('user_permissions')->truncate();
        // DB::table('user_roles')->truncate();
        // DB::table('')
        // // $usersTable       = (new \App\Account\User)->getTable();
        // // $rolesTable       = (new \App\Account\Role)->getTable();
        // // $permissionsTable = (new \App\Account\Permission)->getTable();
        // DB::statement('TRUNCATE TABLE users CASCADE');
        // DB::statement('TRUNCATE TABLE roles CASCADE');
        // DB::statement('TRUNCATE TABLE permissions CASCADE');
        Schema::enableForeignKeyConstraints();
    }
}
