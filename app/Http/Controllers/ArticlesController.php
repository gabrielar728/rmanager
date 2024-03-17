<?php

namespace App\Http\Controllers;

use App\Article;
use App\ArticleCategory;
use App\ArticlesMaterial;
use App\Client;
use App\Material;
use App\Process;
use App\Product;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArticlesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create()
    {
        $categories = DB::table('article_categories')->orderBy('name', 'ASC')->get();
        $clients = DB::table('clients')->orderBy('name', 'ASC')->get();
        $article = DB::table('articles')->get();
        $processes = DB::table('processes')->orderBy('name', 'ASC')->get();
        $material_lists = DB::table('materials')->orderBy('name', 'ASC')->get();
        return view('articles.create', compact('categories', 'clients', 'article', 'material_lists', 'processes'));
    }

    public function postInsertArticleCategory(Request $request)
    {
        if ($request->ajax()) {
            return response(ArticleCategory::create($request->all()));
        }
    }

    public function postInsertClient(Request $request)
    {
        if ($request->ajax()) {
            return response(Client::create($request->all()));
        }
    }

    public function postInsertProcess(Request $request)
    {
        if ($request->ajax()) {
            return response(Process::create($request->all()));
        }
    }

    public function insert(Request $request)
    {
        $this->validate($request, array(
            'category_id' => 'required|integer',
            'name' => 'required|max:255',
            //            'workers_required'    => 'required|integer',
            'client_id' => 'required|integer',
            'material.*' => 'required|integer',
            'quantity.*' => 'required|numeric',
            'process_id.*' => 'required|integer',
            //            'extra.*'             => 'required|boolean',
        ));

        $article = new Article();
        $article->name = $request->name;
        $article->workers_required = 1;
        $article->category_id = $request->category_id;
        $article->client_id = $request->client_id;
        $article->created_at = Carbon::now();
        $article->updated_at = Carbon::now();

        if ($article->save()) {
            $id = $article->id;
            $count = 1;

            foreach ($request->material as $key => $v) {
                $data = array(
                    'row' => $count,
                    'article_id' => $id,
                    'material_id' => $v,
                    'quantity' => $request->quantity[$key],
                    'process_id' => $request->process_id[$key],
                    'extra' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                );

                ArticlesMaterial::insert($data);
                $count++;
            }
            /*$items = count(ArticlesMaterial::where('article_id', $id)->get());
			$countExtra = $items+1;
            if (is_iterable($request->materialExtra)) {
                foreach ($request->materialExtra as $key => $v) {
                    $data = array(
                        'row' => $countExtra,
                        'article_id' => $id,
                        'material_id' => $v,
                        'quantity' => $request->quantityExtra[$key],
                        'process_id' => $request->process_idExtra[$key],
                        'extra' => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),);

                    ArticlesMaterial::insert($data);
                    $countExtra++;
                }
            }*/
        }
        return redirect('articole/adaugare')->with('message', 'Articol adaugat cu succes!');
    }

    public function reportsIndex()
    {
        $article_categories = DB::table('article_categories')->get();
        $clients = DB::table('clients')->get();
        $materials = Material::orderBy('name', 'ASC')->get();

        return view('articles.index', compact('article_categories', 'clients', 'materials'))
            ->with('i');
    }

    function getdata(Request $request)
    {
        $totalData = Article::count();

        $limit = $request->input('length');
        $start = $request->input('start');

        if ($request->from == "" && $request->to == "" && $request->client_id == "" && $request->category_id == "" && $request->materials == "" && $request->status == "") {
            $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                ->join('clients', 'clients.id', '=', 'articles.client_id')
                ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                ->where('articles.id', null)
                ->offset($start)
                ->limit($limit)
                ->orderBy('articles.id', 'desc')
                ->get();

            $totalFiltered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                ->join('clients', 'clients.id', '=', 'articles.client_id')
                ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                ->where('articles.id', null)
                ->count();
        } elseif ($request->from != "" && $request->to != "" && $request->client_id == "" && $request->category_id == "" && $request->materials == "" && $request->status == "") {
            $from = Carbon::parse($request->from)->setTime(0, 0);
            $to = Carbon::parse($request->to)->setTime(23, 59, 59);

            if (empty($request->input('search.value'))) {
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->get();

                $totalFiltered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->count();
            } else {
                $search = $request->input('search.value');
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->get();

                $totalFiltered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->count();
            }
        } elseif ($request->from == "" && $request->to == "" && $request->client_id != "" && $request->category_id == "" && $request->materials == "" && $request->status == "") {
            if (empty($request->input('search.value'))) {
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('clients.id', $request->client_id)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->get();

                $totalFiltered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('clients.id', $request->client_id)
                    ->count();
            } else {
                $search = $request->input('search.value');
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('clients.id', $request->client_id)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->get();

                $totalFiltered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('clients.id', $request->client_id)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->count();
            }
        } elseif ($request->from == "" && $request->to == "" && $request->client_id == "" && $request->category_id != "" && $request->materials == "" && $request->status == "") {
            if (empty($request->input('search.value'))) {
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('article_categories.id', $request->category_id)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->get();

                $totalFiltered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('article_categories.id', $request->category_id)
                    ->count();
            } else {
                $search = $request->input('search.value');
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('article_categories.id', $request->category_id)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->get();

                $totalFiltered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('article_categories.id', $request->category_id)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->count();
            }
        } elseif ($request->from == "" && $request->to == "" && $request->client_id == "" && $request->category_id == "" && $request->materials != "" && $request->status == "") {
            if (empty($request->input('search.value'))) {
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->groupBy('articles_materials.article_id')
                    ->get();

                $filtered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->groupBy('articles_materials.article_id')
                    ->get();
                $totalFiltered = count($filtered);
            } else {
                $search = $request->input('search.value');
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->groupBy('articles_materials.article_id')
                    ->get();

                $filtered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->groupBy('articles_materials.article_id')
                    ->get();
                $totalFiltered = count($filtered);
            }
        } elseif ($request->from == "" && $request->to == "" && $request->client_id == "" && $request->category_id == "" && $request->materials == "" && $request->status != "") {
            if (empty($request->input('search.value'))) {
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('articles.status', $request->status)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->get();

                $totalFiltered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('articles.status', $request->status)
                    ->count();
            } else {
                $search = $request->input('search.value');
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('articles.status', $request->status)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->get();

                $totalFiltered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('articles.status', $request->status)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->count();
            }
        } elseif ($request->from != "" && $request->to != "" && $request->client_id != "" && $request->category_id == "" && $request->materials == "" && $request->status == "") {
            $from = Carbon::parse($request->from)->setTime(0, 0);
            $to = Carbon::parse($request->to)->setTime(23, 59, 59);
            if (empty($request->input('search.value'))) {
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('clients.id', $request->client_id)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->get();

                $totalFiltered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('clients.id', $request->client_id)
                    ->count();
            } else {
                $search = $request->input('search.value');
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('clients.id', $request->client_id)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->get();

                $totalFiltered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('clients.id', $request->client_id)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->count();
            }
        } elseif ($request->from != "" && $request->to != "" && $request->client_id == "" && $request->category_id != "" && $request->materials == "" && $request->status == "") {
            $from = Carbon::parse($request->from)->setTime(0, 0);
            $to = Carbon::parse($request->to)->setTime(23, 59, 59);
            if (empty($request->input('search.value'))) {
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('article_categories.id', $request->category_id)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->get();

                $totalFiltered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('article_categories.id', $request->category_id)
                    ->count();
            } else {
                $search = $request->input('search.value');
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('article_categories.id', $request->category_id)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->get();

                $totalFiltered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('article_categories.id', $request->category_id)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->count();
            }
        } elseif ($request->from != "" && $request->to != "" && $request->client_id == "" && $request->category_id == "" && $request->materials != "" && $request->status == "") {
            $from = Carbon::parse($request->from)->setTime(0, 0);
            $to = Carbon::parse($request->to)->setTime(23, 59, 59);
            if (empty($request->input('search.value'))) {
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->groupBy('articles_materials.article_id')
                    ->get();

                $filtered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->groupBy('articles_materials.article_id')
                    ->get();
                $totalFiltered = count($filtered);
            } else {
                $search = $request->input('search.value');
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->groupBy('articles_materials.article_id')
                    ->get();

                $filtered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->groupBy('articles_materials.article_id')
                    ->get();
                $totalFiltered = count($filtered);
            }
        } elseif ($request->from != "" && $request->to != "" && $request->client_id == "" && $request->category_id == "" && $request->materials == "" && $request->status != "") {
            $from = Carbon::parse($request->from)->setTime(0, 0);
            $to = Carbon::parse($request->to)->setTime(23, 59, 59);

            if (empty($request->input('search.value'))) {
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('articles.status', $request->status)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->get();

                $totalFiltered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('articles.status', $request->status)
                    ->count();
            } else {
                $search = $request->input('search.value');
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('articles.status', $request->status)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->get();

                $totalFiltered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('articles.status', $request->status)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->count();
            }
        } elseif ($request->from == "" && $request->to == "" && $request->client_id != "" && $request->category_id != "" && $request->materials == "" && $request->status == "") {
            if (empty($request->input('search.value'))) {
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('article_categories.id', $request->category_id)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->get();

                $totalFiltered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('article_categories.id', $request->category_id)
                    ->count();
            } else {
                $search = $request->input('search.value');
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('article_categories.id', $request->category_id)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->get();

                $totalFiltered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('article_categories.id', $request->category_id)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->count();
            }
        } elseif ($request->from == "" && $request->to == "" && $request->client_id != "" && $request->category_id == "" && $request->materials != "" && $request->status == "") {
            if (empty($request->input('search.value'))) {
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->groupBy('articles_materials.article_id')
                    ->get();

                $filtered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->groupBy('articles_materials.article_id')
                    ->get();
                $totalFiltered = count($filtered);
            } else {
                $search = $request->input('search.value');
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->groupBy('articles_materials.article_id')
                    ->get();

                $filtered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->groupBy('articles_materials.article_id')
                    ->get();
                $totalFiltered = count($filtered);
            }
        } elseif ($request->from == "" && $request->to == "" && $request->client_id != "" && $request->category_id == "" && $request->materials == "" && $request->status != "") {
            if (empty($request->input('search.value'))) {
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('articles.status', $request->status)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->get();

                $totalFiltered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('articles.status', $request->status)
                    ->count();
            } else {
                $search = $request->input('search.value');
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('articles.status', $request->status)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->get();

                $totalFiltered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('articles.status', $request->status)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->count();
            }
        } elseif ($request->from == "" && $request->to == "" && $request->client_id == "" && $request->category_id != "" && $request->materials != "" && $request->status == "") {
            if (empty($request->input('search.value'))) {
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->groupBy('articles_materials.article_id')
                    ->get();

                $filtered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->groupBy('articles_materials.article_id')
                    ->get();
                $totalFiltered = count($filtered);
            } else {
                $search = $request->input('search.value');
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->groupBy('articles_materials.article_id')
                    ->get();

                $filtered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->groupBy('articles_materials.article_id')
                    ->get();
                $totalFiltered = count($filtered);
            }
        } elseif ($request->from == "" && $request->to == "" && $request->client_id == "" && $request->category_id != "" && $request->materials == "" && $request->status != "") {
            if (empty($request->input('search.value'))) {
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles.status', $request->status)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->get();

                $totalFiltered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles.status', $request->status)
                    ->count();
            } else {
                $search = $request->input('search.value');
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles.status', $request->status)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->get();

                $totalFiltered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles.status', $request->status)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->count();
            }
        } elseif ($request->from == "" && $request->to == "" && $request->client_id == "" && $request->category_id == "" && $request->materials != "" && $request->status != "") {
            if (empty($request->input('search.value'))) {
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->whereIn('articles.status', $request->status)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->groupBy('articles_materials.article_id')
                    ->get();

                $filtered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->whereIn('articles.status', $request->status)
                    ->groupBy('articles_materials.article_id')
                    ->get();
                $totalFiltered = count($filtered);
            } else {
                $search = $request->input('search.value');
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->whereIn('articles.status', $request->status)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->groupBy('articles_materials.article_id')
                    ->get();

                $filtered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->whereIn('articles.status', $request->status)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->groupBy('articles_materials.article_id')
                    ->get();
                $totalFiltered = count($filtered);
            }
        } elseif ($request->from != "" && $request->to != "" && $request->client_id != "" && $request->category_id != "" && $request->materials == "" && $request->status == "") {
            $from = Carbon::parse($request->from)->setTime(0, 0);
            $to = Carbon::parse($request->to)->setTime(23, 59, 59);

            if (empty($request->input('search.value'))) {
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('article_categories.id', $request->category_id)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->get();

                $totalFiltered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('article_categories.id', $request->category_id)
                    ->count();
            } else {
                $search = $request->input('search.value');
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('article_categories.id', $request->category_id)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->get();

                $totalFiltered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('article_categories.id', $request->category_id)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->count();
            }
        } elseif ($request->from != "" && $request->to != "" && $request->client_id != "" && $request->category_id == "" && $request->materials != "" && $request->status == "") {
            $from = Carbon::parse($request->from)->setTime(0, 0);
            $to = Carbon::parse($request->to)->setTime(23, 59, 59);

            if (empty($request->input('search.value'))) {
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->groupBy('articles_materials.article_id')
                    ->get();

                $filtered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->groupBy('articles_materials.article_id')
                    ->get();
                $totalFiltered = count($filtered);
            } else {
                $search = $request->input('search.value');
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->groupBy('articles_materials.article_id')
                    ->get();

                $filtered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->groupBy('articles_materials.article_id')
                    ->get();
                $totalFiltered = count($filtered);
            }
        } elseif ($request->from != "" && $request->to != "" && $request->client_id != "" && $request->category_id == "" && $request->materials == "" && $request->status != "") {
            $from = Carbon::parse($request->from)->setTime(0, 0);
            $to = Carbon::parse($request->to)->setTime(23, 59, 59);

            if (empty($request->input('search.value'))) {
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('articles.status', $request->status)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->get();

                $totalFiltered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('articles.status', $request->status)
                    ->count();
            } else {
                $search = $request->input('search.value');
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('articles.status', $request->status)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->get();

                $totalFiltered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('articles.status', $request->status)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->count();
            }
        } elseif ($request->from != "" && $request->to != "" && $request->client_id == "" && $request->category_id != "" && $request->materials != "" && $request->status == "") {
            $from = Carbon::parse($request->from)->setTime(0, 0);
            $to = Carbon::parse($request->to)->setTime(23, 59, 59);

            if (empty($request->input('search.value'))) {
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->groupBy('articles_materials.article_id')
                    ->get();

                $filtered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->groupBy('articles_materials.article_id')
                    ->get();
                $totalFiltered = count($filtered);
            } else {
                $search = $request->input('search.value');
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->groupBy('articles_materials.article_id')
                    ->get();

                $filtered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->groupBy('articles_materials.article_id')
                    ->get();
                $totalFiltered = count($filtered);
            }
        } elseif ($request->from != "" && $request->to != "" && $request->client_id == "" && $request->category_id != "" && $request->materials == "" && $request->status != "") {
            $from = Carbon::parse($request->from)->setTime(0, 0);
            $to = Carbon::parse($request->to)->setTime(23, 59, 59);

            if (empty($request->input('search.value'))) {
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles.status', $request->status)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->get();

                $totalFiltered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles.status', $request->status)
                    ->count();
            } else {
                $search = $request->input('search.value');
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles.status', $request->status)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->get();

                $totalFiltered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles.status', $request->status)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->count();
            }
        } elseif ($request->from != "" && $request->to != "" && $request->client_id == "" && $request->category_id == "" && $request->materials != "" && $request->status != "") {
            $from = Carbon::parse($request->from)->setTime(0, 0);
            $to = Carbon::parse($request->to)->setTime(23, 59, 59);

            if (empty($request->input('search.value'))) {
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->whereIn('articles.status', $request->status)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->groupBy('articles_materials.article_id')
                    ->get();

                $filtered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->whereIn('articles.status', $request->status)
                    ->groupBy('articles_materials.article_id')
                    ->get();
                $totalFiltered = count($filtered);
            } else {
                $search = $request->input('search.value');
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->whereIn('articles.status', $request->status)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->groupBy('articles_materials.article_id')
                    ->get();

                $filtered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->whereIn('articles.status', $request->status)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->groupBy('articles_materials.article_id')
                    ->get();
                $totalFiltered = count($filtered);
            }
        } elseif ($request->from == "" && $request->to == "" && $request->client_id != "" && $request->category_id != "" && $request->materials != "" && $request->status == "") {
            if (empty($request->input('search.value'))) {
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->groupBy('articles_materials.article_id')
                    ->get();

                $filtered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->groupBy('articles_materials.article_id')
                    ->get();
                $totalFiltered = count($filtered);
            } else {
                $search = $request->input('search.value');
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->groupBy('articles_materials.article_id')
                    ->get();

                $filtered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->groupBy('articles_materials.article_id')
                    ->get();
                $totalFiltered = count($filtered);
            }
        } elseif ($request->from == "" && $request->to == "" && $request->client_id != "" && $request->category_id != "" && $request->materials == "" && $request->status != "") {
            if (empty($request->input('search.value'))) {
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles.status', $request->status)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->get();

                $totalFiltered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles.status', $request->status)
                    ->count();
            } else {
                $search = $request->input('search.value');
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles.status', $request->status)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->get();

                $totalFiltered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles.status', $request->status)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->count();
            }
        } elseif ($request->from == "" && $request->to == "" && $request->client_id == "" && $request->category_id != "" && $request->materials != "" && $request->status != "") {
            if (empty($request->input('search.value'))) {
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->whereIn('articles.status', $request->status)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->groupBy('articles_materials.article_id')
                    ->get();

                $filtered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->whereIn('articles.status', $request->status)
                    ->groupBy('articles_materials.article_id')
                    ->get();
                $totalFiltered = count($filtered);
            } else {
                $search = $request->input('search.value');
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->whereIn('articles.status', $request->status)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->groupBy('articles_materials.article_id')
                    ->get();

                $filtered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->whereIn('articles.status', $request->status)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->groupBy('articles_materials.article_id')
                    ->get();
                $totalFiltered = count($filtered);
            }
        } elseif ($request->from == "" && $request->to == "" && $request->client_id != "" && $request->category_id == "" && $request->materials != "" && $request->status != "") {
            if (empty($request->input('search.value'))) {
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->whereIn('articles.status', $request->status)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->groupBy('articles_materials.article_id')
                    ->get();

                $filtered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->whereIn('articles.status', $request->status)
                    ->groupBy('articles_materials.article_id')
                    ->get();
                $totalFiltered = count($filtered);
            } else {
                $search = $request->input('search.value');
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->whereIn('articles.status', $request->status)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->groupBy('articles_materials.article_id')
                    ->get();

                $filtered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->whereIn('articles.status', $request->status)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->groupBy('articles_materials.article_id')
                    ->get();
                $totalFiltered = count($filtered);
            }
        } elseif ($request->from == "" && $request->to == "" && $request->client_id != "" && $request->category_id != "" && $request->materials != "" && $request->status != "") {
            if (empty($request->input('search.value'))) {
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->whereIn('articles.status', $request->status)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->groupBy('articles_materials.article_id')
                    ->get();

                $filtered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->whereIn('articles.status', $request->status)
                    ->groupBy('articles_materials.article_id')
                    ->get();
                $totalFiltered = count($filtered);
            } else {
                $search = $request->input('search.value');
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->whereIn('articles.status', $request->status)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->groupBy('articles_materials.article_id')
                    ->get();

                $filtered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->whereIn('articles.status', $request->status)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->groupBy('articles_materials.article_id')
                    ->get();
                $totalFiltered = count($filtered);
            }
        } elseif ($request->from != "" && $request->to != "" && $request->client_id == "" && $request->category_id != "" && $request->materials != "" && $request->status != "") {
            $from = Carbon::parse($request->from)->setTime(0, 0);
            $to = Carbon::parse($request->to)->setTime(23, 59, 59);

            if (empty($request->input('search.value'))) {
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->whereIn('articles.status', $request->status)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->groupBy('articles_materials.article_id')
                    ->get();

                $filtered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->whereIn('articles.status', $request->status)
                    ->groupBy('articles_materials.article_id')
                    ->get();
                $totalFiltered = count($filtered);
            } else {
                $search = $request->input('search.value');
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->whereIn('articles.status', $request->status)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->groupBy('articles_materials.article_id')
                    ->get();

                $filtered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->whereIn('articles.status', $request->status)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->groupBy('articles_materials.article_id')
                    ->get();
                $totalFiltered = count($filtered);
            }
        } elseif ($request->from != "" && $request->to != "" && $request->client_id != "" && $request->category_id == "" && $request->materials != "" && $request->status != "") {
            $from = Carbon::parse($request->from)->setTime(0, 0);
            $to = Carbon::parse($request->to)->setTime(23, 59, 59);

            if (empty($request->input('search.value'))) {
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->whereIn('articles.status', $request->status)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->groupBy('articles_materials.article_id')
                    ->get();

                $filtered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->whereIn('articles.status', $request->status)
                    ->groupBy('articles_materials.article_id')
                    ->get();
                $totalFiltered = count($filtered);
            } else {
                $search = $request->input('search.value');
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->whereIn('articles.status', $request->status)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->groupBy('articles_materials.article_id')
                    ->get();

                $filtered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->whereIn('articles.status', $request->status)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->groupBy('articles_materials.article_id')
                    ->get();
                $totalFiltered = count($filtered);
            }
        } elseif ($request->from != "" && $request->to != "" && $request->client_id != "" && $request->category_id != "" && $request->materials == "" && $request->status != "") {
            $from = Carbon::parse($request->from)->setTime(0, 0);
            $to = Carbon::parse($request->to)->setTime(23, 59, 59);

            if (empty($request->input('search.value'))) {
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles.status', $request->status)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->get();

                $totalFiltered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles.status', $request->status)
                    ->count();
            } else {
                $search = $request->input('search.value');
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles.status', $request->status)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->get();

                $totalFiltered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles.status', $request->status)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->count();
            }
        } elseif ($request->from != "" && $request->to != "" && $request->client_id != "" && $request->category_id != "" && $request->materials != "" && $request->status == "") {
            $from = Carbon::parse($request->from)->setTime(0, 0);
            $to = Carbon::parse($request->to)->setTime(23, 59, 59);

            if (empty($request->input('search.value'))) {
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->groupBy('articles_materials.article_id')
                    ->get();

                $filtered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->groupBy('articles_materials.article_id')
                    ->get();
                $totalFiltered = count($filtered);
            } else {
                $search = $request->input('search.value');
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->groupBy('articles_materials.article_id')
                    ->get();

                $filtered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->groupBy('articles_materials.article_id')
                    ->get();
                $totalFiltered = count($filtered);
            }
        } elseif ($request->from != "" && $request->to != "" && $request->client_id != "" && $request->category_id != "" && $request->materials != "" && $request->status != "") {
            $from = Carbon::parse($request->from)->setTime(0, 0);
            $to = Carbon::parse($request->to)->setTime(23, 59, 59);

            if (empty($request->input('search.value'))) {
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->whereIn('articles.status', $request->status)
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->groupBy('articles_materials.article_id')
                    ->get();

                $filtered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->whereIn('articles.status', $request->status)
                    ->groupBy('articles_materials.article_id')
                    ->get();
                $totalFiltered = count($filtered);
            } else {
                $search = $request->input('search.value');
                $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->whereIn('articles.status', $request->status)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('articles.id', 'desc')
                    ->groupBy('articles_materials.article_id')
                    ->get();

                $filtered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                    ->join('clients', 'clients.id', '=', 'articles.client_id')
                    ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                    ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                    ->whereBetween('articles.created_at', array($from, $to))
                    ->whereIn('clients.id', $request->client_id)
                    ->whereIn('article_categories.id', $request->category_id)
                    ->whereIn('articles_materials.material_id', $request->materials)
                    ->whereIn('articles.status', $request->status)
                    ->where('articles.name', 'LIKE', "%{$search}%")
                    ->orWhere('article_categories.name', 'LIKE', "%{$search}%")
                    ->orWhere('clients.name', 'LIKE', "%{$search}%")
                    ->orWhere(DB::raw("(DATE_FORMAT(articles.created_at,'%d.%m.%Y'))"), 'LIKE', "%{$search}%")
                    ->groupBy('articles_materials.article_id')
                    ->get();
                $totalFiltered = count($filtered);
            }
        } else {
            $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                ->join('clients', 'clients.id', '=', 'articles.client_id')
                ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                ->where('articles.id', null)
                ->offset($start)
                ->limit($limit)
                ->orderBy('articles.id', 'desc')
                ->get();

            $totalFiltered = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                ->join('clients', 'clients.id', '=', 'articles.client_id')
                ->select('articles.*', 'articles.id as article_id', 'articles.created_at as created_at_article', 'articles.updated_at as updated_at_article')
                ->where('articles.id', null)
                ->count();
        }

        $data = array();
        if ($articles) {
            foreach ($articles as $article) {
                $exist = Product::where('article_id', $article->article_id)->value('article_id');
                if ($exist === null) {
                    $article_exist = 0;
                } else {
                    $article_exist = 1;
                }

                $nestedData['article_id'] = '<input type="hidden" class="v_id" value="' . $article->id . ' ">';
                $nestedData['article_name'] = $article->name;
                $nestedData['article_category'] = $article->article_category['name'];
                $nestedData['client'] = $article->client['name'];
                if ($article->status == 1) {
                    $nestedData['status'] = '<span class="active_status" style = "color: green;"> activ</span><span class="inactive_status" style = "color: red; display: none;"> inactiv</span>';
                } else {
                    $nestedData['status'] = '<span class="inactive_status" style = "color: red;"> inactiv</span><span class="active_status" style = "color: green; display: none;"> activ</span>';
                }

                $nestedData['created_at'] = Carbon::parse($article->created_at_article)->format('d.m.Y H:i:s');

                $nestedData['details'] = $article->articles_materials->each(function ($articles_material) {
                    $articles_material->material;
                    $articles_material->process_name = $articles_material->process['name'];
                    //                    $articles_material->extra;
                });

                if ($article->status == 1) {
                    if ($article_exist == 0) {
                        $nestedData['action'] = '<button class="btn btn-danger btn-sm inactive_btn">Dezactivare</button><button class="btn btn-success btn-sm active_btn" style="display: none;">Activare</button>  
                                                    <a href="' . route('printArticle', $article->article_id) . '" target="_blank"><img src="' . asset('images/pdf.png') . '"></a>';
                    } else {
                        $nestedData['action'] = '<button class="btn btn-danger btn-sm inactive_btn">Dezactivare</button><button class="btn btn-success btn-sm active_btn" style="display: none;">Activare</button>  
                                                <a href="' . route('printArticle', $article->article_id) . '" target="_blank"><img src="' . asset('images/pdf.png') . '"></a>';
                    }
                } else {
                    if ($article_exist == 0) {
                        $nestedData['action'] = '<button class="btn btn-success btn-sm active_btn">Activare</button><button class="btn btn-danger btn-sm inactive_btn" style="display: none;">Dezactivare</button>
                                                    <a href="' . route('printArticle', $article->article_id) . '" target="_blank"><img src="' . asset('images/pdf.png') . '"></a>';
                    } else {
                        $nestedData['action'] = '<button class="btn btn-success btn-sm active_btn">Activare</button><button class="btn btn-danger btn-sm inactive_btn" style="display: none;">Dezactivare</button>
                                                <a href="' . route('printArticle', $article->article_id) . '" target="_blank"><img src="' . asset('images/pdf.png') . '"></a>';
                    }
                }

                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw" => (int)$request->input('draw'),
            "recordsTotal" => (int)$totalData,
            "recordsFiltered" => $totalFiltered,
            "data" => $data
        );

        echo json_encode($json_data);
    }


    public function deleteCategory(Request $request)
    {
        $exist = Article::where('category_id', '=', $request->id)->first();
        if ($exist === null) {
            ArticleCategory::where('id', $request->id)->delete();
            return response()->json(['success' => true, 'created' => false, 'msg' => 'Stergere cu succes.']);
        }
    }

    public function deleteClient(Request $request)
    {
        $exist = Article::where('client_id', '=', $request->id)->first();
        if ($exist === null) {
            Client::where('id', $request->id)->delete();
            return response()->json(['success' => true, 'created' => false, 'msg' => 'Stergere cu succes.']);
        }
    }


    public function edit($id)
    {
        $materials = DB::table('materials')->get();
        $article = Article::with('article_category', 'client')->findOrFail($id);
        $categories = DB::table('article_categories')->get();
        $clients = DB::table('clients')->get();

        $processes = DB::table('processes')->orderBy('name', 'ASC')->get();

        $rows = count(ArticlesMaterial::where('article_id', $id)->get());


        return view('articles.edit', compact('materials', 'article', 'categories', 'clients', 'processes', 'rows'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, array(
            'category_id' => 'required|integer',
            'name' => 'required|max:255',
            'workers_required' => 'required|integer',
            'client_id' => 'required|integer',
            'material.*' => 'required|integer',
            'quantity.*' => 'required|numeric',
            'process_id.*' => 'required|integer',
            //            'extra.*' => 'required|boolean',
        ));

        $article = Article::findOrFail($id); //se creeaza o noua instanta de tip Articol
        $article->name = $request->name; //se stocheaza numele
        $article->workers_required = $request->workers_required;
        $article->category_id = $request->category_id;
        $article->client_id = $request->client_id;
        $article->created_at = Carbon::now();
        $article->updated_at = Carbon::now();

        if ($article->save()) {
            $id = $article->id;
            $count = 1;
            ArticlesMaterial::where('article_id', $id)->delete();

            foreach ($request->material as $key => $v) {
                $data = array(
                    'row' => $count,
                    'article_id' => $id,
                    'material_id' => $v,
                    'quantity' => $request->quantity[$key],
                    'process_id' => $request->process_id[$key],
                    //                    'extra' => $request->extra[$key],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                );

                ArticlesMaterial::insert($data);
                $count++;
            }
        }
        return redirect()->route('informatii-rapoarte-articole');
    }

    public function findUnit(Request $request)
    {
        $data = Material::select('unit')->where('id', $request->id)->first();
        return response()->json($data);
    }

    public function changeActiveStatus(Request $request)
    {
        $article = Article::find($request->id);
        $article->status = 0;
        $article->updated_at = Carbon::now();
        $article->save();
    }

    public function changeInactiveStatus(Request $request)
    {
        $article = Article::find($request->id);
        $article->status = 1;
        $article->updated_at = Carbon::now();
        $article->save();
    }

    public function verifName(Request $request)
    {
        $exist = Article::where('name', '=', $request->name)->first();
        if ($exist != null) {
            return response()->json(['success' => true, 'created' => true, 'msg' => 'Numele articolului exista in baza de date.']);
        } else {
            return response()->json(['success' => true, 'created' => false, 'msg' => 'OK']);
        }
    }

    public function printArticle($id)
    {
        $current_dateTime = Carbon::now()->format('d.m.Y H:i:s');
        $article = Article::with('client', 'article_category', 'groups', 'articles_materials')->findOrFail($id);

        $pdf = PDF::loadView('articles.printArticle', compact('article'));
        return $pdf->stream('articol_' . $article->name . '_' . $current_dateTime . '.pdf');
    }
}
