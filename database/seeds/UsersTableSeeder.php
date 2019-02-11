<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Role;
use App\Person;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = ['owner','cs','kasir','montir','supplier','sales','konsumen'];

        foreach($roles as $role){
            $this->command->info('Creating Role '. strtoupper($role));

            $r = new Role();
            $r->name = $role;
            $r->save();
            $this->command->info('Creating Person '. strtoupper($role));

            $person = new Person();
            $person->name = $role;
            $person->phoneNumber = $role;
            $person->address = 'address';
            $person->city = 'city';
            $person->role_id = $r->id;
            $person->save();

            if($role == 'owner' || $role == 'cs' || $role == 'kasir'){
                $this->command->info('Creating User '. strtoupper($role));
                $user = new User();
                $user->email = $role.'@app.com';
                $user->password = bcrypt('password');
                $user->people_id = $person->id;
                $user->save();
            }
        }
    }
}
