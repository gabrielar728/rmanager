<?php

namespace App\Http\Controllers;

use App\Article;
use App\ArticlesMaterial;
use App\Dosage;
use App\Material;
use App\Product;
use App\Pump;
use App\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Hoa\Socket\Client;
use Illuminate\Support\Facades\Auth;

class RaspberryPiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:worker');
    }

    public function index()
    {
        $worker = Auth::user();
        $products = Article::join('products','products.article_id','=','articles.id')
            ->join('clients','clients.id', '=', 'articles.client_id')
            ->where('worker_id', $worker->id)
            ->whereDate('products.production_date', '=', Carbon::today()->toDateString())
            ->whereIn('products.status_id', [1,2])
            ->orderBy('products.created_at','desc')
            ->orderBy('products.row','asc')
            ->select('articles.name', 'clients.name as client', 'articles.workers_required', 'products.id as product_id', 'products.article_id', 'products.status_id', 'products.production_date')
            ->get();

        foreach ($products as $key => $product) {
            $product->rows = count(ArticlesMaterial::where('article_id', $product->article_id)->where('extra', 0)->get());
            $product->dosages = count(Dosage::where('product_id', $product->product_id)->get());
        }

        return view('raspberryPi.home', compact('products'));
    }

    public function selectWorkers(Request $request, $id, $worker_id)
    {
        $worker_lastname = Worker::where('id', $worker_id)->value('last');
        $worker_firstname = Worker::where('id', $worker_id)->value('first');

        $dosages = Dosage::where('product_id', $id)->get();

        if (count($dosages) === 0 ) {
            $exist_product_id = Product::where('worker_id', $worker_id)
                ->where('status_id', 2)
                ->value('id');

            if ($exist_product_id) {
                return redirect()->back()->withInput()->with('message', 'Finalizati va rog produsul inceput.');
            } else {
                return view('raspberryPi.selectWorkers', compact('worker_firstname', 'worker_lastname', 'id', 'worker_id'));
            }
        } else {
            $this->productDosage($request, $id, $worker_id);
            return redirect('/productie/login');
        }

    }

    public function addSelectedWorkers(Request $request, $id, $worker_id)
    {
        $product = Product::findOrFail($id);

        $product->workers_nr = $request->workers;
        $product->save();

        $this->productDosage($request, $id, $worker_id);
        return redirect('/productie/login');
    }

    public function productDosage(Request $request, $id, $worker_id)
    {
        $dosages = Dosage::where('product_id', $id)->get();
        $count = count($dosages);
        $exist_product_id = Product::where('worker_id', $worker_id)
            ->where('status_id', 2)
            ->value('id');

        if ($id == $exist_product_id || $exist_product_id == NULL) {
            $port = Pump::where('ip', $request->ip())->value('port');
            $host = 'tcp://' . $request->ip() . ':' . $port;
            $client = new Client($host);
            $client->connect();

            $material_id = Pump::where('ip', $request->ip())->value('material_id');
            $ratio = Pump::where('ip', $request->ip())->value('ratio');
            $article_id = Product::where('id', $id)->value('article_id');
					
			$query = ArticlesMaterial::where('article_id', $article_id)
                    ->where('material_id', $material_id)
                    ->where('row', $count + 1)
                    ->first();

            $this->changeStatus($id);
			$quantity_sql = $query->quantity;

            $final_quantity = $ratio * $quantity_sql;
            $client->writeLine(round($final_quantity));

            $product = Product::findOrFail($id);
            $product_id = $product->id;
            $pump = $request->ip();
            $pump_id = Pump::where('ip', $pump)->value('id');

            $dosage = new Dosage();
            $dosage->product()->associate($product_id);
            $dosage->pump()->associate($pump_id);
            $dosage->material()->associate($material_id);
            $dosage->quantity = $quantity_sql;
            $dosage->created_at = Carbon::now();
            $dosage->updated_at = Carbon::now();

            $dosage->save();
			
			Auth::guard('worker')->logout();
			return redirect('/productie/login');
			
        } else {
            return redirect()->back()->withInput()->with('message', 'Finalizati va rog produsul inceput.');
        }
    }

    public function changeStatus($id)
    {
        $product_status = Product::findOrFail($id);
        $product_status->status_id = 2;
        $product_status->save();
    }

    public function moreResin($id, $worker_id)
    {
        $product_id = $id;
        $worker_lastname = Worker::where('id', $worker_id)->value('last');
        $worker_firstname = Worker::where('id', $worker_id)->value('first');
        $article_id = Product::where('id', $id)->value('article_id');
        $products = ArticlesMaterial::with('material')
            ->where('article_id', $article_id)
            ->where('extra', 1)
            ->orderBy('quantity', 'ASC')
            ->get();
        return view('raspberryPi.moreResin', compact('products', 'worker_lastname', 'worker_firstname', 'product_id'));
    }

    public function productExtraDosage(Request $request, $id)
    {
        $port = Pump::where('ip', $request->ip())->value('port');
        $host = 'tcp://' . $request->ip() . ':' . $port;
        $client = new Client($host);
        $client->connect();

        $material_id = Pump::where('ip', $request->ip())->value('material_id');
        $ratio = Pump::where('id', $material_id)->value('ratio');

        $quantity = $request->more;
        $final_quantity = $ratio * $quantity;
        $client->writeLine(round($final_quantity));

        $product = Product::findOrFail($id);
        $product_id = $product->id;
        $pump = $request->ip();
        $pump_id = Pump::where('ip', $pump)->value('id');

        if($final_quantity > 0) {
            $dosage = new Dosage();
            $dosage->product()->associate($product_id);
            $dosage->pump()->associate($pump_id);
            $dosage->material()->associate($material_id);
            $dosage->quantity = $quantity;
            $dosage->created_at = Carbon::now();
            $dosage->updated_at = Carbon::now();

            $dosage->save();
        }

        Auth::guard('worker')->logout();
        return redirect('/productie/login');
    }

	public function finishProduct($id)
    {
        $product = Product::find($id);
        $product->finished_at = Carbon::now();
        $product->status_id = 4;
        $product->save();

        Auth::guard('worker')->logout();
        return redirect('/productie/login');
    }
	
    public function logoutRaspberryPiWorker()
    {
        Auth::guard('worker')->logout();
        return redirect('/productie/login');
    }

}
