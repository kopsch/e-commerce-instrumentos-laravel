<?php

namespace App\Modules\Account\Users\Database\Seeds;

use Illuminate\Database\Seeder;
use App\Modules\Account\Users\User;
use Illuminate\Support\Facades\Hash;
use App\Modules\Account\Profiles\Profile;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedAdminTestUser();
        $this->seedRegionalTestUser();
    }

    public function seedAdminTestUser()
    {
        User::create([
            'name'       => 'Admin Teste',
            'email'      => 'admin.test@prestes.com',
            'password'   => Hash::make(env('LOCAL_TEST_USER_PASSWORD')),
            'profile_id' => Profile::where('name', 'Administrativo')->firstOrFail()->id,
            'active'     => 1
        ]);
    }

    public function seedRegionalTestUser()
    {
        User::create([
            'name'       => 'User Teste',
            'email'      => 'person.test@prestes.com',
            'password'   => Hash::make(env('LOCAL_TEST_USER_PASSWORD')),
            'profile_id' => Profile::where('name', 'Usuário')->firstOrFail()->id,
            'active'     => 1
        ]);
    }
}
