<?php

use App\Account\Permission;
use App\Account\Role;
use App\Account\Team;
use App\Account\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class FullSeeder extends Seeder
{
    protected $map = [
        'b'   => 'browse',
        'r'   => 'read',
        'e'   => 'edit',
        'a'   => 'add',
        'd'   => 'delete',
        'bo'  => 'browse_owned',
        'ro'  => 'read_owned',
        'eo'  => 'edit_owned',
        'ao'  => 'add_owned',
        'do'  => 'delete_owned',
        'rot' => 'remove_from_owned_team',
        'bt'  => 'browse_team',
        'rt'  => 'read_team',
    ];

    protected $roles = [
        'id|name|display_name|description',
        '1|admin|Admin|Admin',
        '2|employee|Employee|Employee',
        '3|customer|Customer|Customer',
        '4|team_captain|Team Captain|Team Captain',
    ];

    /**
     * Role Format:
     * role_id1,role_id2:role_id2_team,team_id,team_id2
     */
    protected $users = [
        'id|first_name|last_name|email|roles',
        '1|Patrick|Admin|admin@app.com|1',
        '2|John|Employee|employee@app.com|2',
        '3|Eliot|Customer|eliot@app.com|3',
        '4|James|Tiberius|james@app.com|3,4',
        '5|Hunter|Okkes|hunter@app.com|3,4',
        '6|Bernie|Landers|bernie@app.com|3,4',
        '7|Liz|Warden|liz@app.com|3,4',
    ];

    protected $teams =[
        'id|name|slug|owner_id|users',
        '1|Microsoft|microsoft|4|3,4,6',
        '2|Google|google|5|4,5,6,7',
        '3|Amazon|amazon|6|3,6',
        '4|Facebook|facebook|7|7'
    ];

    protected $resources = [
        'users' => [
            '1|b,r,e,a,d',
            '2|b,r,e,a',
            '4|bo,ro,rot'

        ],
        'acl' => [
            '1|b,r,e,a,d',
            '2|b,r',
        ],
        'teams' => [
            '1|b,r,e,a,d',
            '2|b,r,e,a',
            '3|r,a',
            '4|r,e,a'
        ],
        'profile' => [
            '1|r,e',
            '2|r,e',
            '3|r,e',
            '4|r,e',
        ],
        'mailboxes' => [
            '1|b,r,e,a,d',
            '2|b,r',
            '3|b,r,e,a,d',
            '4|b,r,e,a,d',
        ],
        'messages' => [
            '1|b,r,e,a,d',
            '2|b,r',
            '3|b,r,e,a,d',
            '4|b,r,e,a,d',
        ],
        'tags' => [
            '1|b,r,e,a,d',
            '2|b,r,e,a,d',
            '3|b,r,e,a,d',
            '4|b,r,e,a,d',
        ]
    ];

    private function simpleFormatter($resource, $type)
    {
        $key               = explode('|', $this->$type[0]);
        $resource          = explode('|', $resource);
        $data              = [];

        foreach ($key as $k => $v) {
            $data[$v] = $resource[$k];
        }

        return $data;
    }

    public function seedRoles()
    {
        foreach ($this->roles as $r => $role) {
            if ($r > 0) {
                $role = $this->simpleFormatter($role, 'roles');
                Role::create($role);
                unset($role);
            }
        }
    }

    public function seedUsers()
    {
        $users = [];

        foreach ($this->users as $k => $user) {
            if ($k > 0) {
                $user  = $this->simpleFormatter($user, 'users');
                $roles = explode(',', $user['roles']);
                unset($user['roles']);
                $user['password'] = 'test1234';
                $user             = User::create($user);
                $user->roles()->sync($roles);
            }
        }
    }

    public function seedTeams()
    {
        foreach ($this->teams as $k=>$team) {
            if ($k > 0) {
                $team  = $this->simpleFormatter($team, 'teams');
                $users = explode(',', $team['users']);
                unset($team['users']);
                $team = Team::create($team);
                $team->users()->sync($users);
            }
        }
    }

    public function seedPermissions()
    {
        $map = $this->map;

        foreach ($this->resources as $resource => $permissions) {
            foreach ($permissions as $permission) {
                $data    = explode('|', $permission);
                $role    = $data[0];
                $actions = explode(',', $data[1]);

                foreach ($actions as $action) {
                    $display_name = ucwords("{$map[$action]} {$resource}");
                    $p            = Permission::firstOrCreate([
                        'display_name' => $display_name,
                        'name'         => str_slug($display_name),
                        'description'  => $display_name
                    ]);
                    $p->roles()->attach($role);
                }
            }
        }
    }

    public function run()
    {
        // Model::unguard();
        $this->truncateTables();
        $this->seedRoles();
        $this->seedUsers();
        $this->seedTeams();
        $this->seedPermissions();
        // Model::guard();
    }

    /**
     * Truncates all the laratrust tables and the users table
     *
     * @return    void
     */
    public function truncateTables()
    {
        Schema::disableForeignKeyConstraints();
        $tables = [
            'role_permissions',
            'user_permissions',
            'user_roles',
            'users',
            'teams',
            'team_users',
            'teamables',
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

    // $role_permissions = [
//     'role|resource|actions',
//     '1|users|b,r,e,a,d',
//     '1|acl|b,r,e,a,d',
//     '1|teams|b,r,e,a,d',
//     '1|profile|r,e',
//     '2|users|b,r,e',
//     '2|acl|b,r,',
//     '2|teams|b,r,e,a',
//     '2|profile|r,e',
//     '3|users|b,r,e,a,d',
//     '3|acl|b,r,e,a,d',
//     '3|teams|b,r,e,a,d',
//     '3|profile|r,e',
// ]
}
