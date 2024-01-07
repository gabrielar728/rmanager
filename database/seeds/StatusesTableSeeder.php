<?php

use Illuminate\Database\Seeder;
use App\Status;

class StatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $status1 = new Status();
        $status1->status = 'lansat';
        $status1->save();

        $status2 = new Status();
        $status2->status = 'in lucru';
        $status2->save();

        $status3 = new Status();
        $status3->status = 'anulat';
        $status3->save();

        $status4 = new Status();
        $status4->status = 'finalizat';
        $status4->save();
    }
}
