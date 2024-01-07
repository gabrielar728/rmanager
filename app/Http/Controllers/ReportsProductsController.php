<?php

namespace App\Http\Controllers;

use App\ArticlesMaterial;
use App\Dosage;
use App\Group;
use App\Product;
use App\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;

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

        return view('products.reports.indexReports', compact('team_leaders', 'groups'));
    }

    function getdata(Request $request)
    {
        $totalData = Product::count();

        $limit = $request->input('length');
        $start = $request->input('start');


        //$order = $columns[$request->input('order.0.column')];
        //$dir = $request->input('order.0.dir');

        if($limit == -1){
            if($request->from=="" && $request->to=="" && $request->group_id=="" && $request->worker_id=="" && $request->status=="" ) {
                $reports = Product::with('worker')
                    ->join('articles', 'articles.id', '=', 'products.article_id')
                    ->join('groups', 'groups.id', '=', 'products.group_id')
                    ->join('statuses', 'statuses.id', '=', 'products.status_id')
                    ->join('workers','workers.id','=','products.worker_id')
                    ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                    ->where('products.id', null)
                    ->orderBy('products.id', 'DESC')
                    ->get();

                $totalFiltered = Product::with('worker')
                    ->join('articles', 'articles.id', '=', 'products.article_id')
                    ->join('groups', 'groups.id', '=', 'products.group_id')
                    ->join('statuses', 'statuses.id', '=', 'products.status_id')
                    ->join('workers','workers.id','=','products.worker_id')
                    ->where('products.id', 0)
                    ->count();
            } elseif ($request->from != "" && $request->to != "" && $request->group_id == "" && $request->worker_id == "" && $request->status == "") {
                $from = Carbon::parse($request->from)->setTime(0, 0);
                $to = Carbon::parse($request->to)->setTime(23, 59, 59);

                if (empty($request->input('search.value'))) {
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->count();
                } else {
                    $search = $request->input('search.value');
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->count();
                }

            } elseif ($request->from == "" && $request->to == "" && $request->group_id != "" && $request->worker_id == "" && $request->status == "") {

                if (empty($request->input('search.value'))) {
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereIn('groups.id' , $request->group_id)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereIn('groups.id', $request->group_id)
                        ->count();
                } else {
                    $search = $request->input('search.value');
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereIn('groups.id' , $request->group_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereIn('groups.id', $request->group_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->count();
                }

            } elseif ($request->from == "" && $request->to == "" && $request->group_id == "" && $request->worker_id != "" && $request->status == "") {
                if (empty($request->input('search.value'))) {
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereIn('workers.id', $request->worker_id)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereIn('workers.id', $request->worker_id)
                        ->count();
                } else {
                    $search = $request->input('search.value');
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereIn('workers.id', $request->worker_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereIn('workers.id', $request->worker_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->count();
                }

            } elseif ($request->from == "" && $request->to == "" && $request->group_id == "" && $request->worker_id == "" && $request->status != "") {
                if (empty($request->input('search.value'))) {
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereIn('products.status_id', $request->status)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereIn('products.status_id', $request->status)
                        ->count();
                } else {
                    $search = $request->input('search.value');
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereIn('products.status_id', $request->status)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereIn('products.status_id', $request->status)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->count();
                }

            } elseif ($request->from != "" && $request->to != "" && $request->group_id != "" && $request->worker_id == "" && $request->status == "") {

                $from = Carbon::parse($request->from)->setTime(0, 0);
                $to = Carbon::parse($request->to)->setTime(23, 59, 59);

                if (empty($request->input('search.value'))) {
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('groups.id', $request->group_id)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('groups.id', $request->group_id)
                        ->count();
                } else {
                    $search = $request->input('search.value');
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('groups.id', $request->group_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('groups.id', $request->group_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->count();
                }

            } elseif ($request->from != "" && $request->to != "" && $request->group_id == "" && $request->worker_id != "" && $request->status == "") {

                $from = Carbon::parse($request->from)->setTime(0, 0);
                $to = Carbon::parse($request->to)->setTime(23, 59, 59);

                if (empty($request->input('search.value'))) {
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('workers.id', $request->worker_id)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('workers.id', $request->worker_id)
                        ->count();
                } else {
                    $search = $request->input('search.value');
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('workers.id', $request->worker_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->whereIn('workers.id', $request->worker_id)
                        ->count();
                }

            } elseif ($request->from != "" && $request->to != "" && $request->group_id == "" && $request->worker_id == "" && $request->status != "") {

                $from = Carbon::parse($request->from)->setTime(0, 0);
                $to = Carbon::parse($request->to)->setTime(23, 59, 59);

                if (empty($request->input('search.value'))) {
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('products.status_id', $request->status)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('products.status_id', $request->status)
                        ->count();
                } else {
                    $search = $request->input('search.value');
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('products.status_id', $request->status)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('products.status_id', $request->status)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->count();
                }


            } elseif ($request->from == "" && $request->to == "" && $request->group_id == "" && $request->worker_id != "" && $request->status != "") {
                if (empty($request->input('search.value'))) {
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('products.status_id', $request->status)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('products.status_id', $request->status)
                        ->count();
                } else {
                    $search = $request->input('search.value');
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('products.status_id', $request->status)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('products.status_id', $request->status)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->count();
                }

            } elseif ($request->from != "" && $request->to != "" && $request->group_id == "" && $request->worker_id != "" && $request->status != "") {

                $from = Carbon::parse($request->from)->setTime(0, 0);
                $to = Carbon::parse($request->to)->setTime(23, 59, 59);

                if (empty($request->input('search.value'))) {
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('products.status_id', $request->status)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('products.status_id', $request->status)
                        ->count();
                } else {
                    $search = $request->input('search.value');
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('products.status_id', $request->status)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('products.status_id', $request->status)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->count();
                }

            } elseif ($request->from == "" && $request->to == "" && $request->group_id != "" && $request->worker_id != "" && $request->status == "") {
                if (empty($request->input('search.value'))) {
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('groups.id', $request->group_id)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('groups.id', $request->group_id)
                        ->count();
                } else {
                    $search = $request->input('search.value');
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('groups.id', $request->group_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('groups.id', $request->group_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->count();
                }

            } elseif ($request->from == "" && $request->to == "" && $request->group_id != "" && $request->worker_id == "" && $request->status != "") {
                if (empty($request->input('search.value'))) {
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereIn('products.status_id', $request->status)
                        ->whereIn('groups.id', $request->group_id)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereIn('products.status_id', $request->status)
                        ->whereIn('groups.id', $request->group_id)
                        ->count();
                } else {
                    $search = $request->input('search.value');
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereIn('products.status_id', $request->status)
                        ->whereIn('groups.id', $request->group_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereIn('products.status_id', $request->status)
                        ->whereIn('groups.id', $request->group_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->count();
                }

            } elseif ($request->from == "" && $request->to == "" && $request->group_id != "" && $request->worker_id != "" && $request->status != "") {
                if (empty($request->input('search.value'))) {
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('products.status_id', $request->status)
                        ->whereIn('groups.id', $request->group_id)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('products.status_id', $request->status)
                        ->whereIn('groups.id', $request->group_id)
                        ->count();
                } else {
                    $search = $request->input('search.value');
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('products.status_id', $request->status)
                        ->whereIn('groups.id', $request->group_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('products.status_id', $request->status)
                        ->whereIn('groups.id', $request->group_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->count();
                }

            } elseif ($request->from != "" && $request->to != "" && $request->group_id != "" && $request->worker_id == "" && $request->status != "") {

                $from = Carbon::parse($request->from)->setTime(0, 0);
                $to = Carbon::parse($request->to)->setTime(23, 59, 59);

                if (empty($request->input('search.value'))) {
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('products.status_id', $request->status)
                        ->whereIn('groups.id', $request->group_id)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('products.status_id', $request->status)
                        ->whereIn('groups.id', $request->group_id)
                        ->count();
                } else {
                    $search = $request->input('search.value');
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('products.status_id', $request->status)
                        ->whereIn('groups.id', $request->group_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('products.status_id', $request->status)
                        ->whereIn('groups.id', $request->group_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->count();
                }

            } elseif ($request->from != "" && $request->to != "" && $request->group_id != "" && $request->worker_id != "" && $request->status == "") {

                $from = Carbon::parse($request->from)->setTime(0, 0);
                $to = Carbon::parse($request->to)->setTime(23, 59, 59);

                if (empty($request->input('search.value'))) {
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('groups.id', $request->group_id)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('groups.id', $request->group_id)
                        ->count();
                } else {
                    $search = $request->input('search.value');
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('groups.id', $request->group_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('groups.id', $request->group_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->count();
                }

            } elseif ($request->from != "" && $request->to != "" && $request->group_id != "" && $request->worker_id != "" && $request->status != "") {

                $from = Carbon::parse($request->from)->setTime(0, 0);
                $to = Carbon::parse($request->to)->setTime(23, 59, 59);

                if (empty($request->input('search.value'))) {
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('products.status_id', $request->status)
                        ->whereIn('groups.id', $request->group_id)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('products.status_id', $request->status)
                        ->whereIn('groups.id', $request->group_id)
                        ->count();
                } else {
                    $search = $request->input('search.value');
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('products.status_id', $request->status)
                        ->whereIn('groups.id', $request->group_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('products.status_id', $request->status)
                        ->whereIn('groups.id', $request->group_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->count();
                }

            } else {
                $reports = Product::with('worker')
                    ->join('articles', 'articles.id', '=', 'products.article_id')
                    ->join('groups', 'groups.id', '=', 'products.group_id')
                    ->join('statuses', 'statuses.id', '=', 'products.status_id')
                    ->join('workers','workers.id','=','products.worker_id')
                    ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                    ->where('products.id', null)
                    ->orderBy('products.id', 'DESC')
                    ->get();

                $totalFiltered = Product::with('worker')
                    ->join('articles', 'articles.id', '=', 'products.article_id')
                    ->join('groups', 'groups.id', '=', 'products.group_id')
                    ->join('statuses', 'statuses.id', '=', 'products.status_id')
                    ->join('workers','workers.id','=','products.worker_id')
                    ->where('products.id', 0)
                    ->count();
            }
//with limit
        } else {
            if($request->from=="" && $request->to=="" && $request->group_id=="" && $request->worker_id=="" && $request->status=="" ) {
                $reports = Product::with('worker')
                    ->join('articles', 'articles.id', '=', 'products.article_id')
                    ->join('groups', 'groups.id', '=', 'products.group_id')
                    ->join('statuses', 'statuses.id', '=', 'products.status_id')
                    ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                    ->join('workers','workers.id','=','products.worker_id')
                    ->where('products.id', null)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('products.id', 'DESC')
                    ->get();

                $totalFiltered = Product::with('worker')
                    ->join('articles', 'articles.id', '=', 'products.article_id')
                    ->join('groups', 'groups.id', '=', 'products.group_id')
                    ->join('statuses', 'statuses.id', '=', 'products.status_id')
                    ->join('workers','workers.id','=','products.worker_id')
                    ->where('products.id', 0)
                    ->count();
            } elseif ($request->from != "" && $request->to != "" && $request->group_id == "" && $request->worker_id == "" && $request->status == "") {
                $from = Carbon::parse($request->from)->setTime(0, 0);
                $to = Carbon::parse($request->to)->setTime(23, 59, 59);

                if (empty($request->input('search.value'))) {
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->count();
                } else {
                    $search = $request->input('search.value');
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->count();
                }

            } elseif ($request->from == "" && $request->to == "" && $request->group_id != "" && $request->worker_id == "" && $request->status == "") {

                if (empty($request->input('search.value'))) {
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereIn('groups.id' , $request->group_id)
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereIn('groups.id', $request->group_id)
                        ->count();
                } else {
                    $search = $request->input('search.value');
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereIn('groups.id' , $request->group_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereIn('groups.id', $request->group_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->count();
                }

            } elseif ($request->from == "" && $request->to == "" && $request->group_id == "" && $request->worker_id != "" && $request->status == "") {
                if (empty($request->input('search.value'))) {
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereIn('workers.id', $request->worker_id)
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereIn('workers.id', $request->worker_id)
                        ->count();
                } else {
                    $search = $request->input('search.value');
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereIn('workers.id', $request->worker_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereIn('workers.id', $request->worker_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->count();
                }

            } elseif ($request->from == "" && $request->to == "" && $request->group_id == "" && $request->worker_id == "" && $request->status != "") {
                if (empty($request->input('search.value'))) {
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereIn('products.status_id', $request->status)
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereIn('products.status_id', $request->status)
                        ->count();
                } else {
                    $search = $request->input('search.value');
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereIn('products.status_id', $request->status)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereIn('products.status_id', $request->status)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->count();
                }

            } elseif ($request->from != "" && $request->to != "" && $request->group_id != "" && $request->worker_id == "" && $request->status == "") {

                $from = Carbon::parse($request->from)->setTime(0, 0);
                $to = Carbon::parse($request->to)->setTime(23, 59, 59);

                if (empty($request->input('search.value'))) {
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('groups.id', $request->group_id)
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('groups.id', $request->group_id)
                        ->count();
                } else {
                    $search = $request->input('search.value');
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('groups.id', $request->group_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('groups.id', $request->group_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->count();
                }

            } elseif ($request->from != "" && $request->to != "" && $request->group_id == "" && $request->worker_id != "" && $request->status == "") {

                $from = Carbon::parse($request->from)->setTime(0, 0);
                $to = Carbon::parse($request->to)->setTime(23, 59, 59);

                if (empty($request->input('search.value'))) {
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('workers.id', $request->worker_id)
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('workers.id', $request->worker_id)
                        ->count();
                } else {
                    $search = $request->input('search.value');
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('workers.id', $request->worker_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->whereIn('workers.id', $request->worker_id)
                        ->count();
                }

            } elseif ($request->from != "" && $request->to != "" && $request->group_id == "" && $request->worker_id == "" && $request->status != "") {

                $from = Carbon::parse($request->from)->setTime(0, 0);
                $to = Carbon::parse($request->to)->setTime(23, 59, 59);

                if (empty($request->input('search.value'))) {
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('products.status_id', $request->status)
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('products.status_id', $request->status)
                        ->count();
                } else {
                    $search = $request->input('search.value');
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('products.status_id', $request->status)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('products.status_id', $request->status)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->count();
                }


            } elseif ($request->from == "" && $request->to == "" && $request->group_id == "" && $request->worker_id != "" && $request->status != "") {
                if (empty($request->input('search.value'))) {
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('products.status_id', $request->status)
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('products.status_id', $request->status)
                        ->count();
                } else {
                    $search = $request->input('search.value');
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('products.status_id', $request->status)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('products.status_id', $request->status)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->count();
                }

            } elseif ($request->from != "" && $request->to != "" && $request->group_id == "" && $request->worker_id != "" && $request->status != "") {

                $from = Carbon::parse($request->from)->setTime(0, 0);
                $to = Carbon::parse($request->to)->setTime(23, 59, 59);

                if (empty($request->input('search.value'))) {
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('products.status_id', $request->status)
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('products.status_id', $request->status)
                        ->count();
                } else {
                    $search = $request->input('search.value');
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('products.status_id', $request->status)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('products.status_id', $request->status)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->count();
                }

            } elseif ($request->from == "" && $request->to == "" && $request->group_id != "" && $request->worker_id != "" && $request->status == "") {
                if (empty($request->input('search.value'))) {
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('groups.id', $request->group_id)
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('groups.id', $request->group_id)
                        ->count();
                } else {
                    $search = $request->input('search.value');
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('groups.id', $request->group_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('groups.id', $request->group_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->count();
                }

            } elseif ($request->from == "" && $request->to == "" && $request->group_id != "" && $request->worker_id == "" && $request->status != "") {
                if (empty($request->input('search.value'))) {
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereIn('products.status_id', $request->status)
                        ->whereIn('groups.id', $request->group_id)
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereIn('products.status_id', $request->status)
                        ->whereIn('groups.id', $request->group_id)
                        ->count();
                } else {
                    $search = $request->input('search.value');
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereIn('products.status_id', $request->status)
                        ->whereIn('groups.id', $request->group_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereIn('products.status_id', $request->status)
                        ->whereIn('groups.id', $request->group_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->count();
                }

            } elseif ($request->from == "" && $request->to == "" && $request->group_id != "" && $request->worker_id != "" && $request->status != "") {
                if (empty($request->input('search.value'))) {
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('products.status_id', $request->status)
                        ->whereIn('groups.id', $request->group_id)
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('products.status_id', $request->status)
                        ->whereIn('groups.id', $request->group_id)
                        ->count();
                } else {
                    $search = $request->input('search.value');
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('products.status_id', $request->status)
                        ->whereIn('groups.id', $request->group_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('products.status_id', $request->status)
                        ->whereIn('groups.id', $request->group_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->count();
                }

            } elseif ($request->from != "" && $request->to != "" && $request->group_id != "" && $request->worker_id == "" && $request->status != "") {

                $from = Carbon::parse($request->from)->setTime(0, 0);
                $to = Carbon::parse($request->to)->setTime(23, 59, 59);

                if (empty($request->input('search.value'))) {
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('products.status_id', $request->status)
                        ->whereIn('groups.id', $request->group_id)
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('products.status_id', $request->status)
                        ->whereIn('groups.id', $request->group_id)
                        ->count();
                } else {
                    $search = $request->input('search.value');
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('products.status_id', $request->status)
                        ->whereIn('groups.id', $request->group_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('products.status_id', $request->status)
                        ->whereIn('groups.id', $request->group_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->count();
                }

            } elseif ($request->from != "" && $request->to != "" && $request->group_id != "" && $request->worker_id != "" && $request->status == "") {

                $from = Carbon::parse($request->from)->setTime(0, 0);
                $to = Carbon::parse($request->to)->setTime(23, 59, 59);

                if (empty($request->input('search.value'))) {
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('groups.id', $request->group_id)
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('groups.id', $request->group_id)
                        ->count();
                } else {
                    $search = $request->input('search.value');
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('groups.id', $request->group_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('groups.id', $request->group_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->count();
                }

            } elseif ($request->from != "" && $request->to != "" && $request->group_id != "" && $request->worker_id != "" && $request->status != "") {

                $from = Carbon::parse($request->from)->setTime(0, 0);
                $to = Carbon::parse($request->to)->setTime(23, 59, 59);

                if (empty($request->input('search.value'))) {
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('products.status_id', $request->status)
                        ->whereIn('groups.id', $request->group_id)
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('products.status_id', $request->status)
                        ->whereIn('groups.id', $request->group_id)
                        ->count();
                } else {
                    $search = $request->input('search.value');
                    $reports = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('products.status_id', $request->status)
                        ->whereIn('groups.id', $request->group_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('products.id', 'DESC')
                        ->get();

                    $totalFiltered = Product::with('worker')
                        ->join('articles', 'articles.id', '=', 'products.article_id')
                        ->join('groups', 'groups.id', '=', 'products.group_id')
                        ->join('statuses', 'statuses.id', '=', 'products.status_id')
                        ->join('workers','workers.id','=','products.worker_id')
                        ->whereBetween('products.production_date', array($from, $to))
                        ->whereIn('workers.id', $request->worker_id)
                        ->whereIn('products.status_id', $request->status)
                        ->whereIn('groups.id', $request->group_id)
                        ->where('articles.name', 'LIKE', "%{$search}%")
                        ->orWhere('groups.name', 'LIKE', "%{$search}%")
                        ->orWhere('statuses.status', 'LIKE', "%{$search}%")
                        ->orWhere(DB::raw("(DATE_FORMAT(products.production_date,'%d.%m.%Y'))"),'LIKE', "%{$search}%")
                        ->count();
                }

            } else {
                $reports = Product::with('worker')
                    ->join('articles', 'articles.id', '=', 'products.article_id')
                    ->join('groups', 'groups.id', '=', 'products.group_id')
                    ->join('statuses', 'statuses.id', '=', 'products.status_id')
                    ->join('workers','workers.id','=','products.worker_id')
                    ->select('products.id as product_id', 'products.*', 'products.created_at as created_at_products')
                    ->where('products.id', null)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('products.id', 'DESC')
                    ->get();

                $totalFiltered = Product::with('worker')
                    ->join('articles', 'articles.id', '=', 'products.article_id')
                    ->join('groups', 'groups.id', '=', 'products.group_id')
                    ->join('statuses', 'statuses.id', '=', 'products.status_id')
                    ->join('workers','workers.id','=','products.worker_id')
                    ->where('products.id', 0)
                    ->count();
            }
        }

        $data = array();
        if ($reports) {
            foreach ($reports as $report) {
                $first_dosage = Dosage::where('product_id', $report->product_id)->value('created_at');
                $articles_materials_rows = ArticlesMaterial::where('article_id', $report->article_id)
                    ->where('extra', 0)
                    ->get();
                $dosages_rows = Dosage::where('product_id', $report->product_id)->get();

                if (count($dosages_rows) > count($articles_materials_rows)) {
                    $nestedData['extra'] = '<i class="fa fa-exclamation-triangle" style="font-size: 16px; color:red"></i>';
                } else {
                    $nestedData['extra'] = '';
                }

                $nestedData['article_name'] = $report->article['name'];
                $nestedData['group_name'] = $report->group['name'];
                $nestedData['workers_nr'] = $report->workers_nr === 0 ? '-' : $report->workers_nr;
                $nestedData['created_at'] = Carbon::parse($report->created_at_products)->format('d.m.Y H:i:s');
                if ($first_dosage == null) {
                    $nestedData['taken_at'] = 'nepreluat';
                } else {
                    $nestedData['taken_at'] = Carbon::parse($first_dosage)->format('d.m.Y H:i:s');
                }

                if ($report->status_id == 1) {
                    $nestedData['status'] = '<span class="gen_st" style="color: blue;">lansat</span>';
                } elseif ($report->status_id == 2) {
                    $nestedData['status'] = '<span class="gen_st" style="color: orange;">in lucru</span>';
                } elseif ($report->status_id == 3) {
                    $nestedData['status'] = '<span class="gen_st" style="color: red;">anulat</span>';
                } elseif ($report->status_id == 4) {
                    $nestedData['status'] = '<span class="gen_st" style="color: green;">finalizat</span>';
                }

                if (Carbon::parse($report->production_date)->setTime(23, 59, 59) < Carbon::now()->setTime(0, 0) && $report->status_id == 1) {
                    $nestedData['production_date'] = '<span style="color: red;">' . Carbon::parse($report->production_date)->format("d.m.Y") . '</span>';
                } elseif (Carbon::parse($report->production_date)->setTime(23, 59, 59) < Carbon::now()->setTime(0, 0) && $report->status_id == 2) {
                    $nestedData['production_date'] = '<span class="p_date"><input type="date" id="' . $report->product_id . '" name="production_date" class="form-control production_date" value="' . $report->production_date . '"></span>';
                } elseif ($report->production_date == NULL) {
                    $nestedData['production_date'] = '-';
                } else {
                    $nestedData['production_date'] = Carbon::parse($report->production_date)->format('d.m.Y');
                }

                if ($report->finished_at == NULL) {
                    if ($report->status_id == 2) {
                        $nestedData['finished_at'] = '<a href="' . route('finishProductApp', $report->product_id) . '" class="btn btn-success btn-sm" onclick="return confirm(\'Finalizati produsul?\')">Finalizare</a>';
                    } else {
                        $nestedData['finished_at'] = 'nefinalizat';
                    }
                } else {
                    $nestedData['finished_at'] = Carbon::parse($report->finished_at)->format('d.m.Y H:i:s');
                }

                if ($report->finished_at == NULL) {
                    $nestedData['exec_time'] = '<span class="update_time">00:00:00</span>';
                } else {
                    $nestedData['exec_time'] = '<span class="update_time">' . gmdate('H:i:s', Carbon::parse($first_dosage)->diffInSeconds(Carbon::parse($report->finished_at))) . '</span>';
                }

                if (count($first_dosage)) {
                    $nestedData['details'] = $report->dosages->each(function ($dosage) {
                        $dosage->articles_materials_rows = count(ArticlesMaterial::where('article_id', $dosage->product['article_id'])
                            ->where('extra', 0)
                            ->get());
                        $dosage->team_leader = $dosage->product->worker;
                        $dosage->material;
                    });

                } else {
                    $nestedData['details'] = null;
                }

                $nestedData['action'] = '<a href="' . route('printProduct', $report->product_id) . '" target="_blank"><img src="' . asset('images/pdf.png') . '" /></a>';

                $data[] = $nestedData;

            }
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
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

        $pdf = PDF::loadView('products.reports.printProduct',compact('product', 'dosages'));
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
