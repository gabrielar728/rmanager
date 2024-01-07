<?php

use Illuminate\Database\Seeder;
use App\User;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminG = new User();
        $adminG->first_name = 'Rotariu';
        $adminG->last_name = 'Gabriela';
        $adminG->email = 'gabriela@romwell.ro';
        $adminG->password = bcrypt('gabi123');
        $adminG->created_at = Carbon::now();
        $adminG->updated_at = Carbon::now();
        $adminG->save();
        $adminG->roles()->sync([1]);

        $adminA = new User();
        $adminA->first_name = 'Borsos';
        $adminA->last_name = 'Alin';
        $adminA->email = 'alin@romwell.ro';
        $adminA->password = bcrypt('alin123');
        $adminA->created_at = Carbon::now();
        $adminA->updated_at = Carbon::now();
        $adminA->save();
        $adminA->roles()->sync([1]);
    }
}
