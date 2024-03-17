<?php

namespace App\Http\Controllers;

use App\ArticlesMaterial;
use App\Dosage;
use App\Group;
use App\Product;
use App\Pump;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use DataTables;
use Illuminate\Http\Request;

class ReportsProductsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    function indexReports()
    {
        $team_leaders = Product::with('worker')->groupBy('worker_id')->orderBy('worker_id', 'DESC')->get();
        $groups = Group::where('status', 1)->orderBy('id', 'DESC')->get();
        $pumps = Pump::orderBy('id', 'ASC')->get();

        return view('products.reports.indexReports', compact('team_leaders', 'groups', 'pumps'));
    }

    public function getdata(Request $request)
    {
        $totalData = Product::count();

        $draw = $request->get('draw');
        $pageNumber = ($request->start / $request->length) + 1;
        $pageLength = $request->length;
        $skip = ($pageNumber - 1) * $pageLength;

        $products = Product::with('worker')
            ->join('articles', 'articles.id', '=', 'products.article_id')
            ->join('groups', 'groups.id', '=', 'products.group_id')
            ->join('statuses', 'statuses.id', '=', 'products.status_id')
            ->join('workers', 'workers.id', '=', 'products.worker_id')
            ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
            ->orderBy('products.id', 'DESC');

        if ($request->from == "" || $request->to == "") {

            $reports = $products->where('products.id', null);

        }
        if ($request->from != "" && $request->to != "" && $request->group_id == "" && $request->worker_id == "" && $request->status == "") {

            $from = Carbon::parse($request->from)->setTime(0, 0);
            $to = Carbon::parse($request->to)->setTime(23, 59, 59);

            $reports = $products->whereBetween('products.production_date', array($from, $to));
        }
        if ($request->from != "" && $request->to != "" && $request->group_id != "" && $request->worker_id == "" && $request->status == "") {

            $from = Carbon::parse($request->from)->setTime(0, 0);
            $to = Carbon::parse($request->to)->setTime(23, 59, 59);

            $reports = $products->whereBetween('products.production_date', array($from, $to))
                ->whereIn('groups.id', $request->group_id);
        }
        if ($request->from != "" && $request->to != "" && $request->group_id == "" && $request->worker_id != "" && $request->status == "") {

            $from = Carbon::parse($request->from)->setTime(0, 0);
            $to = Carbon::parse($request->to)->setTime(23, 59, 59);

            $reports = $products->whereBetween('products.production_date', array($from, $to))
                ->whereIn('workers.id', $request->worker_id);
        }
        if ($request->from != "" && $request->to != "" && $request->group_id == "" && $request->worker_id == "" && $request->status != "") {

            $from = Carbon::parse($request->from)->setTime(0, 0);
            $to = Carbon::parse($request->to)->setTime(23, 59, 59);

            $reports = $products->whereBetween('products.production_date', array($from, $to))
                ->whereIn('products.status_id', $request->status);
        }
        if ($request->from != "" && $request->to != "" && $request->group_id == "" && $request->worker_id != "" && $request->status != "") {

            $from = Carbon::parse($request->from)->setTime(0, 0);
            $to = Carbon::parse($request->to)->setTime(23, 59, 59);

            $reports = $products->whereBetween('products.production_date', array($from, $to))
                ->whereIn('workers.id', $request->worker_id)
                ->whereIn('products.status_id', $request->status);
        }
        if ($request->from != "" && $request->to != "" && $request->group_id != "" && $request->worker_id == "" && $request->status != "") {

            $from = Carbon::parse($request->from)->setTime(0, 0);
            $to = Carbon::parse($request->to)->setTime(23, 59, 59);

            $reports = $products->whereBetween('products.production_date', array($from, $to))
                ->whereIn('products.status_id', $request->status)
                ->whereIn('groups.id', $request->group_id);
        }
        if ($request->from != "" && $request->to != "" && $request->group_id != "" && $request->worker_id != "" && $request->status == "") {

            $from = Carbon::parse($request->from)->setTime(0, 0);
            $to = Carbon::parse($request->to)->setTime(23, 59, 59);

            $reports = $products->whereBetween('products.production_date', array($from, $to))
                ->whereIn('workers.id', $request->worker_id)
                ->whereIn('groups.id', $request->group_id);
        }
        if ($request->from != "" && $request->to != "" && $request->group_id != "" && $request->worker_id != "" && $request->status != "") {

            $from = Carbon::parse($request->from)->setTime(0, 0);
            $to = Carbon::parse($request->to)->setTime(23, 59, 59);

            $reports = $products->whereBetween('products.production_date', array($from, $to))
                ->whereIn('workers.id', $request->worker_id)
                ->whereIn('products.status_id', $request->status)
                ->whereIn('groups.id', $request->group_id);
        }

        $data = array();
        $reports = $reports->get();

        if ($reports) {

            foreach ($reports as $report) {
                $first_dosage = Dosage::where('product_id', $report->product_id)->value('created_at');
                $articles_materials_rows = ArticlesMaterial::where('article_id', $report->article_id)
                    ->where('extra', 0)
                    ->get();

                $nestedData['article_name'] = $report->article['name'];

                $nestedData['flowopt_name'] = $report->product ?: "-";
                $nestedData['serial_no'] = $report->serial_no ?: "-";
                $nestedData['sales_order'] = $report->sales_order ?: "-";

                $nestedData['group_name'] = $report->group['name'] === 'none' ? '-' : $report->group['name'];

//                $nestedData['workers_nr'] = $report->workers_nr === 0 ? '-' : $report->workers_nr;

                if ($report->status_id == 1 && (Carbon::parse($report->production_date)->weekOfYear !== Carbon::now()->weekOfYear)) {
                    $nestedData['production_date'] = Carbon::parse($report->production_date)->year . ', ' . Carbon::parse($report->production_date)->weekOfYear;
                } elseif ($report->status_id == 2 && (Carbon::parse($report->production_date)->weekOfYear !== Carbon::now()->weekOfYear)) {
//                    $nestedData['production_date'] = '<span class="p_date"><input type="week" id="' . $report->product_id . '" name="production_date" class="form-control production_date" value="' . $report->production_date_week . '"></span>';
                    $nestedData['production_date'] = $report->production_date_week;
                } elseif ($report->production_date === NULL) {
                    $nestedData['production_date'] = '-';
                } else {
                    $nestedData['production_date'] = Carbon::parse($report->production_date)->year . ', ' . Carbon::parse($report->production_date)->weekOfYear;
                }

                if ($first_dosage == null) {
                    $nestedData['taken_at'] = 'nepreluat';
                } else {
                    $nestedData['taken_at'] = Carbon::parse($first_dosage)->format('d.m.Y H:i:s');
                }

                $report->finished_at === NULL ? $nestedData['finished_at'] = 'nefinalizat' : $nestedData['finished_at'] = Carbon::parse($report->finished_at)->format('d.m.Y H:i:s');

                /*if ($report->finished_at === NULL) {
                    if ($report->status_id === 2) {
                        $nestedData['finished_at'] = '<a href="' . route('finishProductApp', $report->product_id) . '" class="btn btn-success btn-sm" onclick="return confirm(\'Finalizati produsul?\')">Finalizare</a>';
                    } else {
                        $nestedData['finished_at'] = 'nefinalizat';
                    }
                } else {
                    $nestedData['finished_at'] = Carbon::parse($report->finished_at)->format('d.m.Y H:i:s');
                }*/
//                $nestedData['created_at'] = Carbon::parse($report->created_at_products)->format('d.m.Y H:i:s');

                /*if ($report->status_id == 1) {
                    $nestedData['status'] = '<span class="gen_st" style="color: blue;">lansat</span>';
                } elseif ($report->status_id == 2) {
                    $nestedData['status'] = '<span class="gen_st" style="color: orange;">in lucru</span>';
                } elseif ($report->status_id == 3) {
                    $nestedData['status'] = '<span class="gen_st" style="color: red;">anulat</span>';
                } elseif ($report->status_id == 4) {
                    $nestedData['status'] = '<span class="gen_st" style="color: green;">finalizat</span>';
                }*/

                if ($report->status_id == 1) {
                    $nestedData['status'] = 'lansat';
                } elseif ($report->status_id == 2) {
                    $nestedData['status'] = 'in lucru';
                } elseif ($report->status_id == 3) {
                    $nestedData['status'] = 'anulat';
                } elseif ($report->status_id == 4) {
                    $nestedData['status'] = 'finalizat';
                }

                if ($report->finished_at === NULL) {
                    $nestedData['exec_time'] = '00:00:00';
                } else {
                    $nestedData['exec_time'] = gmdate('H:i:s', Carbon::parse($first_dosage)->diffInSeconds(Carbon::parse($report->finished_at)));
                }

                $nestedData['action'] = '<a href="' . route('printProduct', $report->product_id) . '" target="_blank"><img src="' . asset('images/pdf.png') . '" /></a>';

                $nestedData['details'] = [];

                if (count((array)$first_dosage)) {

                    $nestedData['details'] = $report->dosages->each(function ($dosage) use ($articles_materials_rows) {
                        $dosage->articles_materials_rows = count($articles_materials_rows);
                        $dosage->team_leader = $dosage->product->worker;
                        $dosage->material;
                        $dosage->pump;
                    });
                    $nestedData['total_resin'] = '<strong>' . $report->dosages->sum('quantity') . '</strong>';

                    if ($request->pump !== null) {
                        $nestedData['details'] = $report->dosages->where('pump_id', $request->pump)->each(function ($dosage) use ($articles_materials_rows) {
                            $dosage->articles_materials_rows = count($articles_materials_rows);
                            $dosage->team_leader = $dosage->product->worker;
                            $dosage->material;
                            $dosage->pump;
                        });
                        $nestedData['total_resin'] = $report->dosages->sum('quantity');
                    }

                } else {
                    $nestedData['details'] = [];
                    $nestedData['total_resin'] = 0;
                }

                if ($request->pump !== null && count($nestedData['details']) > 0) {
                    $data[] = $nestedData;
                }
                if ($request->pump === null) {
                    $data[] = $nestedData;
                }
            }
        }

        $json_data = array(
            "draw" => (int)$draw,
            "recordsTotal" => (int)$totalData,
            "recordsFiltered" => collect($data)->count(),
            "data" => $data,
        );

        echo json_encode($json_data);
    }

    public function printProduct($id)
    {
        $product = Product::with('article')->findOrFail($id);
        $product->first_dosage = Dosage::where('product_id', $id)->value('created_at');
        $dosages = Dosage::where('product_id', $id)->get();

        $article_id = Product::where('id', $id)->value('article_id');
        $product->articles_materials_rows = ArticlesMaterial::where('article_id', $article_id)
            ->where('extra', 0)
            ->get();

        $pdf = PDF::loadView('products.reports.printProduct', compact('product', 'dosages'));
        return $pdf->stream('produs.pdf');
    }

    public function finishProductApp($id)
    {
        $product = Product::find($id);
        $product->finished_at = Carbon::now();
        $product->status_id = 4;
        $product->updated_at = Carbon::now();
        $product->save();
        return redirect()->route('informatii-rapoarte-produse');
    }
}
