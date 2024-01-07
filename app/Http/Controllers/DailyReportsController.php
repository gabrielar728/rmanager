<?php

namespace App\Http\Controllers;

use App\Client;
use App\Dosage;
use App\Product;
use App\Worker;
use Carbon\Carbon;
use DatePeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DailyReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    function index()
    {
        $team_leaders =  Worker::where('status', 1)->orderBy('last', 'asc')->get();
        $clients = Client::orderBy('name', 'ASC')->get();

        return view('dailyReports.index', compact('team_leaders', 'clients'));
    }

    function getdata(Request $request)
    {
        $from = Carbon::parse($request->from)->setTime(0, 0);
        $to = Carbon::parse($request->to)->setTime(23, 59, 59);

        $workers_id = $request->worker_id;
        $clients_id = $request->client_id;
        $workers = Worker::select('id', 'first', 'last')->whereIn('id', $workers_id)->orderBy('last', 'asc')->get();

        $days = Product::select(array(DB::raw('DATE(finished_at) product_finished_at')))
            ->whereBetween('finished_at', array($from, $to))
            ->groupBy('product_finished_at')
            ->orderBy('id', 'desc')
            ->get();

        $workers->each(function($worker) use(&$from, &$to, &$days, &$clients_id) {
            $sql = Product::select(array(DB::raw('DATE(finished_at) product_finished_at')))
                ->where('worker_id', $worker->id)
                ->whereBetween('finished_at', array($from, $to))
                ->groupBy('product_finished_at')
                ->orderBy('id', 'desc')
                ->get();

            foreach ($sql as $v)
            {
                $v->products = Product::select('products.id as product_id', 'articles.id as article_id', 'articles.name as product_name', 'products.workers_nr', 'products.finished_at')
                    ->join('articles', 'articles.id', '=', 'products.article_id')
                    ->where('worker_id', $worker->id)
                    ->whereIn('articles.client_id', $clients_id)
                    ->whereDate('finished_at', '=', $v->product_finished_at)
                    ->orderBy('finished_at', 'DESC')
                    ->get();

                $sum = strtotime('00:00:00');
                $totaltime = 0;
                foreach ($v->products as $product_item) {
                    $first_dosage = Dosage::where('product_id', $product_item->product_id)->value('created_at');
                    $exec_time = gmdate('H:i:s', Carbon::parse($first_dosage)->diffInSeconds(Carbon::parse($product_item->finished_at)));
                    $product_item->product_exec_time = $exec_time;
                    //$product_item->sum = Carbon::createFromFormat('H:i:s',$sum)->addHours(intval($exec_time));
                    $timeinsec = strtotime($exec_time) - $sum;
                    $totaltime = $totaltime + $timeinsec;

                    $dosages_rows = Dosage::select(DB::raw('SUM(quantity) as totalQuantity'))
                                        ->where('product_id', $product_item->product_id)
                                        ->value('totalQuantity');

                    $product_item->total_product_quantity = number_format($dosages_rows, 2);

                }

                $h = intval($totaltime / 3600);
                $totaltime = $totaltime - ($h * 3600);
                $m = intval($totaltime / 60);
                $s = $totaltime - ($m * 60);
                $v->day_total_hours = $h . "h " . $m . "m " . $s . "s";
                unset($sum);
                unset($totaltime);
            }
            $worker->products = $sql;

        });

        return response()->json(['days'=>$days, 'workers'=>$workers]);
    }


}
