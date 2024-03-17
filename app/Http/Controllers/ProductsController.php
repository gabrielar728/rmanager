<?php

namespace App\Http\Controllers;

use App\Article;
use App\ArticleGroup;
use App\Group;
use App\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class ProductsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $groups = DB::table('groups')->where('status', 1)->orderBy('id', 'DESC')->get();
        $workers = DB::table('workers')->where('status', 1)->orderBy('first', 'ASC')->get();
        return view('products.index', compact('groups', 'workers'));
    }

    public function createProduct(Request $request, $item_name)
    {
        try {
            $this->validate($request, array(
                'item_id' => 'required|integer',
                'worker_id' => 'required|integer',
                'production_date' => 'required|string'
            ));

            $item_id = $request->item_id;
            $worker_id = $request->worker_id;

            if ($item_name == 'group') {
                $articles_groups = ArticleGroup::with('article')
                    ->with('group')
                    ->where('group_id', $item_id)
                    ->orderBy('row', 'ASC')
                    ->get();

                $count = 1;

                foreach ($articles_groups as $articles_group) {
                    $data = array(
                        'row' => $count,
                        'article_id' => $articles_group->article_id,
                        'group_id' => $item_id,
                        'status_id' => 1,
                        'worker_id' => $worker_id,
                        'initial_production_date' => Carbon::parse($request->production_date),
                        'production_date' => Carbon::parse($request->production_date),
                        'production_date_week' => $request->production_date,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now());

                    Product::insert($data);
                    $count++;
                }
            } else {
                $data = array(
                    'row' => 1,
                    'article_id' => $item_id,
                    'group_id' => 1,
                    'status_id' => 1,
                    'worker_id' => $worker_id,
                    'initial_production_date' => Carbon::parse($request->production_date),
                    'production_date' => Carbon::parse($request->production_date),
                    'production_date_week' => $request->production_date,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now());

                Product::insert($data);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public function showGroups()
    {
        $groups = DB::table('groups')->where('status', 1)->orderBy('id', 'DESC')->get();

        return response(['groups' => $groups]);
    }

    public function showArticles()
    {
        $articles = DB::table('articles')->where('status', 1)->orderBy('id', 'DESC')->get();

        return response(['articles' => $articles]);
    }

    public function showGroupsArticles()
    {
        $groups = DB::table('groups')->where('status', 1)->orderBy('id', 'DESC')->get();
        $articles = DB::table('articles')->where('status', 1)->orderBy('id', 'DESC')->get();

        return response(['groups' => $groups, 'articles' => $articles]);
    }

    public function getArticles(Request $request)
    {
        $id = $request->id;
        $item_name = $request->item_name;
        if ($item_name == 'group') {
            $group_name = Group::where('id', $id)->value('name');
            $articles_groups = ArticleGroup::with('article')
                ->with('group')
                ->where('group_id', $id)
                ->get();
            return View::make('products.ajaxShowGroup')->with('articles_groups', $articles_groups)->with('group_name', $group_name);
        } else {
            $articles = Article::join('article_categories', 'article_categories.id', '=', 'articles.category_id')
                ->join('clients', 'clients.id', '=', 'articles.client_id')
                ->join('articles_materials', 'articles_materials.article_id', '=', 'articles.id')
                ->where('articles.id', '=', $id)
                ->select('articles.*', 'articles.name as article_name')
                ->orderBy('articles.id', 'desc')
                ->groupBy('articles_materials.article_id')
                ->get();

            $material = Article::with('articles_materials')->findOrFail($id);
            return View::make('products.ajaxShowArticle')->with('articles', $articles)->with('material', $material);
        }
    }

    public function assignedTo(Request $request)
    {
        $assignee_id = $request->assignee_id;
        $product_id = $request->product_id;
        $data = Product::findOrFail($product_id);

        $data->worker_id = $assignee_id;
        $data->save();
    }

    public function productionDate(Request $request)
    {
        $production_date_week = $request->production_date;
        $product_id = $request->product_id;
        $data = Product::findOrFail($product_id);

        $data->production_date = Carbon::parse($request->production_date);
        $data->production_date_week = $production_date_week;
        $data->save();
    }

    public function cancel($id)
    {
        $product_cancel = Product::findOrFail($id);
        $product_cancel->status_id = 3;
        $product_cancel->save();
        $this->showProductInformation();
    }

    public function showProductInformation()
    {
        $products = $this->ProductInformation();
        $workers = DB::table('workers')->where('status', 1)->orderBy('first', 'ASC')->get();
        return view('products.productsInfo', compact('products', 'workers'));
    }

    public function ProductInformation()
    {
        return Product::where('status_id', '=', 1)
            ->orderBy('created_at', 'desc')
            ->orderBy('row', 'asc')
            ->get();
    }

}
