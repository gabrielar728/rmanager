<?php

namespace App\Console\Commands;

use App\Product;
use Carbon\Carbon;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class productionDateNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:productionDateNotification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'verify if the production date is pass over';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {   //sunt stocate produsele finalizate cu o zi in urma
		$from_y = Carbon::yesterday()->startOfDay();
		$to_y =  Carbon::yesterday()->endOfDay();
        $yesterday_products = Product::whereBetween('finished_at', array($from_y, $to_y))
            ->where('status_id', 4)
            ->get();
        //sunt stocate toate produsele finalizate
        $total_products = Product::where('status_id', 4)->get();
        //sunt stocate toate produsele care au depasit data productiei
        $overdue_products = Product::where('production_date', '<', Carbon::now()->startOfDay())
            ->where('status_id', [1, 2])
            ->get();

		$current_day = Carbon::now()->format( 'l' );
        $from = Carbon::parse(Carbon::now()->subDays(3))->startOfDay();
        $to = Carbon::parse(Carbon::now()->subDays(3))->endOfDay();
        $friday_products = Product::whereBetween('finished_at', array($from, $to))
            ->where('status_id', 4)
            ->get();

        foreach ($overdue_products as $overdue_product) {
            $datetime1 = new DateTime(Carbon::now());
            $datetime2 = new DateTime($overdue_product->production_date);
            $interval = $datetime1->diff($datetime2);
            $overdue_product->days = $interval->format('%a');
            
        }
        $data = [
            'yesterday_products' => $yesterday_products,
            'total_products' => $total_products,
            'overdue_products' => $overdue_products,
            'current_day' => $current_day,
            'friday_products' => $friday_products
        ];
		
        $current_date = Carbon::now();
        if (!$current_date->isWeekend()) {
            //se trimite e-mail daca ziua curenta nu este weekend
            Mail::send('email.due_dateNotification', $data, function ($message) use ($data) {
                $message->from('rmanager@arplama.ro', 'rmanager@arplama.ro');
                $message->to('ciprian.cocan@arplama.ro');
                $message->subject('rManager - Raport produse ' . Carbon::now()->format('d.m.Y'));
            });
        }
    }
}
