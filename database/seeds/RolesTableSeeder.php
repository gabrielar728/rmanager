<?php

use Illuminate\Database\Seeder;
use App\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role_admin = new Role();
        $role_admin->name = 'Admin';
        $role_admin->description = 'Acces la toata aplicatia';
        $role_admin->save();

        $role_ingineri = new Role();
        $role_ingineri->name = 'Ingineri';
        $role_ingineri->description = 'Acces la tot in afara de administrare';
        $role_ingineri->save();

        $role_hr = new Role();
        $role_hr->name = 'HR';
        $role_hr->description = 'Acces doar la personal';
        $role_hr->save();

        $role_magazie = new Role();
        $role_magazie->name = 'Magazie';
        $role_magazie->description = 'Acces la facturi, materiale si stoc';
        $role_magazie->save();
    }
}
