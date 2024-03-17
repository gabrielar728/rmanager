<?php

namespace App\Http\Controllers;

use App\Article;
use App\ArticlesMaterial;
use App\Dosage;
use App\Product;
use App\Pump;
use App\Worker;
use Carbon\Carbon;
use Hoa\Socket\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;

class RaspberryPiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:worker');
    }

    public function index()
    {
        $worker = Auth::user();
        $products = Article::join('products', 'products.article_id', '=', 'articles.id')
            ->join('clients', 'clients.id', '=', 'articles.client_id')
            ->where('worker_id', $worker->id)
//            ->whereDate('products.production_date', '=', Carbon::today()->toDateString())
            ->whereIn('products.status_id', [1, 2])
            ->orderBy('products.created_at', 'desc')
            ->orderBy('products.row', 'asc')
            ->select('articles.name', 'clients.name as client', 'articles.workers_required', 'products.id as product_id', 'products.article_id', 'products.status_id', 'products.production_date', 'products.scanned_barcode')
            ->get();

        return view('raspberryPi.home', compact('products'));
    }

    public function searchProducts(Request $request)
    {
        try {
            if ($request->ajax() && preg_match('(SerialNO|SalesOrder|Product)', $request->search) === 1) {
                $output = '';
                $bar_code_url = explode("&", $request->search);
                $serial_no = array_filter(explode("SerialNO=", $bar_code_url[2]));
                $sales_order = array_filter(explode("SalesOrder=", $bar_code_url[3]));
                $product_name = array_filter(explode("Product=", $bar_code_url[4]));

                $product = Product::with('article')->where('serial_no', array_shift($serial_no))
                    ->where('sales_order', array_shift($sales_order))
                    ->where('product', array_shift($product_name))
                    ->first();

                if ($product) {
                    if ($product->status_id === 1 || $product->status_id === 2) {
                        $product->user_id = Auth::user()->id;
                        $output .= '<tr style="margin-top: 2%;">' .
                            '<td style="padding-left: 10px; vertical-align: middle;">1</td>' .
                            '<td style="padding-left: 10px; vertical-align: middle;">' . $product->article['name'] . '</td>' .
                            '<td style="padding-left: 10px; vertical-align: middle;">' . $product->article->client['name'] . '</td>' .
                            '<td style="padding-left: 10px; vertical-align: middle;">' . ($product->status_id == 1 ? '<span style="color: blue;">nou</span>' :
                                ($product->status_id == 2 ? '<span style="color: orange;">in lucru</span>' : '')) . '</td>' .
                            '<td style="padding-left: 10px; vertical-align: middle;">' . (($product->status_id == 1 || $product->status_id == 2) && (Carbon::parse($product->production_date)->weekOfYear === Carbon::now()->weekOfYear) && (Carbon::parse($product->production_date)->year === Carbon::now()->year) ?
                                '<span style="color: green;"> ' . Carbon::parse($product->production_date)->year . ', ' . Carbon::parse($product->production_date)->weekOfYear . '</span>' :
                                '<span style="color: red;">' . Carbon::parse($product->production_date)->year . ', ' . Carbon::parse($product->production_date)->weekOfYear . '</span>') . '</td>';

                        return response()->json(['output' => $output, 'product' => $product]);
                    }
                    if ($product->status_id === 4) {
                        return response()->json(['success' => true, 'created' => true, 'msg' => 'Produsul a fost deja finalizat.']);
                    }
                }
            }
            return response()->json(['success' => true, 'created' => true, 'msg' => 'Produsul nu a fost gasit. Codul de bare nu a fost salvat sau este gresit.']);
        }
        catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public function barcodeScan(Request $request, $id, $worker_id)
    {
        $worker_lastname = Worker::where('id', $worker_id)->value('last');
        $worker_firstname = Worker::where('id', $worker_id)->value('first');
        $product = Product::with('article')->where('id', $id)->first();

       /* $dosages = Dosage::where('product_id', $id)->get();

        if (count($dosages) === 0) {
            $exist_product_id = Product::where('worker_id', $worker_id)
                ->where('status_id', 2)
                ->value('id');

            if ($exist_product_id) {
                return redirect()->back()->withInput()->with('message', 'Finalizati va rog produsul inceput.');
            }

        }*/
        return view('raspberryPi.barcodeScan', compact('worker_firstname', 'worker_lastname', 'id', 'worker_id', 'product'));
    }

    /*public function productDosage(Request $request, $id, $worker_id)
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
    }*/

    public function addBarcodeFields(Request $request, $id, $worker_id)
    {
        try {
            if (preg_match('(SerialNO|SalesOrder|Product)', $request->bar_code_url) === 1) {
                $product = Product::findOrFail($id);
                $bar_code_url = explode("&", $request->bar_code_url);

                $serial_no = array_filter(explode("SerialNO=", $bar_code_url[2]));
                $sales_order = array_filter(explode("SalesOrder=", $bar_code_url[3]));
                $product_name = array_filter(explode("Product=", $bar_code_url[4]));

//                $product_existing = Product::where('serial_no', $serial_no)->first();
//                if ($product_existing) {
//                    return redirect()->back()->with('message', 'Exista un produs salvat cu aceasta serie. Fiecare produs are o serie unica, nu se pot salva doua produse cu acelasi numar de serie. Va rugam sa scanati alt cod de bare.');
//                }
                $product->workers_nr = "1";
                $product->serial_no = array_shift($serial_no);
                $product->sales_order = array_shift($sales_order);
                $product->product = array_shift($product_name);
                $product->scanned_barcode = 1;
                $product->save();
                Log::info("Barcode fields saved for the Product ID: " . $product->id . " by the Worker ID: " . $worker_id);

            } else {
                return redirect()->back()->with('message', 'Codul scanat nu are formatul corect.');
            }

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            if (strpos($e->getMessage(), "Integrity constraint violation")) {
                return redirect()->back()->with('message', "Exista un produs salvat cu aceasta serie. Fiecare produs are o serie unica, nu se pot salva doua produse cu acelasi numar de serie. Va rugam sa scanati alt cod de bare.");
            }
        }

        return Redirect::to('/productie/' . $id . '/optiuni-cantitati/' . $worker_id);
    }

    public function selectQuantity($id, $worker_id)
    {
        $product = Product::with('article')->findOrFail($id);
        $worker_lastname = Worker::where('id', $worker_id)->value('last');
        $worker_firstname = Worker::where('id', $worker_id)->value('first');
        $article_id = Product::where('id', $id)->value('article_id');
        $quantities = ArticlesMaterial::with('material')
            ->where('article_id', $article_id)
            ->orderBy('quantity', 'ASC')
            ->get();

        return view('raspberryPi.selectQuantity', compact('quantities', 'worker_lastname', 'worker_firstname', 'product'));
    }

    public function productDosageOptions(Request $request, $id, $worker_id)
    {
        try {
            $port = Pump::where('ip', $request->ip())->value('port');
            $host = 'tcp://' . $request->ip() . ':' . $port;
            $client = new Client($host);
            $client->connect();

            $material_id = Pump::where('ip', $request->ip())->value('material_id');
            $ratio = Pump::where('id', $material_id)->value('ratio');

            $quantity = $request->quantity;
            $final_quantity = $ratio * $quantity;
            $client->writeLine(round($final_quantity));

            $pump = $request->ip();
            $pump_id = Pump::where('ip', $pump)->value('id');

            $dosage = new Dosage();
            $dosage->product()->associate($id);
            $dosage->pump()->associate($pump_id);
            $dosage->material()->associate($material_id);
            $dosage->quantity = $quantity;
            $dosage->created_at = Carbon::now();
            $dosage->updated_at = Carbon::now();

            if ($dosage->save()) {
                $this->changeStatus($id);
                Log::info("Dosage saved for the Product ID: " . $id . " by the Worker ID: " . $worker_id);
                Auth::guard('worker')->logout();
                return redirect('/productie/login');
            }

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $ip = $request->ip();
            if ($e->getMessage() == "Client cannot join tcp://$ip:.") {
                return response('Aplicatia nu se poate conecta la pompa cu IP-ul ' . $request->ip() .
                    '<form id="logout-form" action="' . route("raspberryPiWorker.logout") . '" method="POST">
                    <button type="submit" class="btn" style="margin-top: 20px; padding: 10px 15px; background-color: red; color: white; border: 2px solid red; border-radius: 3px;">DECONECTARE</button>' .
                    csrf_field() .
                    '</form>', 404);
            } else {
                return response()->json(['message' => $e->getMessage()], 404);
            }
        }

    }

    public function changeStatus($id)
    {
        $product_status = Product::findOrFail($id);
        $product_status->status_id = 2;
        $product_status->save();
    }

    public function finishProduct($id)
    {
        $product = Product::find($id);
        $product->finished_at = Carbon::now();
        $product->status_id = 4;

        if ($product->save()) {
            $first_dosage = Dosage::where('product_id', $product->id)->value('created_at');
            $product->start_date = Carbon::parse($first_dosage)->format('d.m.Y H:i:s');
            $product->exec_time = gmdate('H:i:s', Carbon::parse($first_dosage)->diffInSeconds(Carbon::parse($product->finished_at)));
            $product->total_resin = $product->dosages->sum('quantity');

            $data = [
                'product' => $product,
            ];

            if (config('settings.finish_product_email_notifications')) {
                Mail::send('email.finishedProduct', $data, static function ($message) {
                    $message->from('rmanager@arplama.ro', 'rmanager@arplama.ro');
                    $message->to(config('settings.finish_product_email_notifications'));
                    $message->subject('rManager - Finished Product ' . Carbon::now()->format('d.m.Y'));
                });
            }
        }

        Auth::guard('worker')->logout();
        return redirect('/productie/login');
    }

    public function logoutRaspberryPiWorker()
    {
        Auth::guard('worker')->logout();
        return redirect('/productie/login');
    }

}
