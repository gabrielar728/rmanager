<?php

namespace App\Http\Controllers;

use App\Group;
use App\Material;
use App\Product;
use App\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \BladeView|bool|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $newProducts = Product::with('article')
            ->where('status_id', '=', 1)
            ->orderBy('id', 'desc')
            ->get();

        $processProducts = Product::with('article')
            ->where('status_id', '=', 2)
            ->orderBy('id', 'desc')
            ->get();

        $monthOuts = Material::orderBy('name', 'ASC')->get();;
        foreach ($monthOuts as $key => $monthOut) {
            $sum_dosages = DB::table('dosages')
                ->where('material_id', $monthOut->id)
                ->whereMonth('created_at', '=', Carbon::now()->month)
                ->sum('quantity');

            $monthOut->consum = number_format((float)$sum_dosages, 2, '.', '');
        }

        $yearOuts = Material::orderBy('name', 'ASC')->get();;
        foreach ($yearOuts as $key => $yearOut) {
            $sum_dosages = DB::table('dosages')
                ->where('material_id', $yearOut->id)
                ->whereYear('created_at', '=', Carbon::now()->year)
                ->sum('quantity');

            $yearOut->consum = number_format((float)$sum_dosages, 2, '.', '');
        }
        return view('home', compact('newProducts', 'processProducts', 'monthOuts', 'yearOuts'));
    }

    public function notifications()
    {
        return view('notifications');
    }

    public function addEmailNotification(Request $request)
    {
        Setting::set('finish_product_email_notifications', $request->input('finish_product_email_notifications'));

        return redirect()->back();
    }
}
