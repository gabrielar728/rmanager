<?php

use Illuminate\Database\Seeder;
use App\Group;
use Illuminate\Support\Carbon;

class GroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $group1 = new Group();
        $group1->name = 'none';
        $group1->status = 0;
        $group1->created_at = Carbon::now();
        $group1->updated_at = Carbon::now();
        $group1->save();
    }
}
