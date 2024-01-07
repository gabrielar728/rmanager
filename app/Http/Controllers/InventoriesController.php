<?php

namespace App\Http\Controllers;

use App\Inventory;
use App\Material;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;

class InventoriesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

   public function getManageOut()
   {
       $years = DB::table('dosages')->orderBy('created_at', 'DESC')->groupBy(DB::raw("YEAR(created_at)"))->get();
       return view('operations.out.index', compact('years'));
   }

   public function showOutInformation(Request $request)
   {
       if($request->year!="" && $request->month=="" && $request->ytd=="" && $request->mtd=="" && $request->zero=="")
       {
           $inventories = $this->showOut();
           foreach($inventories as $key=>$inventory)
           {
               $sum_dosages = DB::table('dosages')
                   ->where('material_id', $inventory->id)
                   ->whereYear('created_at', '=', $request->year)
                   ->sum('quantity');

               $inventory->consum  = $sum_dosages;
           }

           return view('operations.out.outInfo', compact('inventories'));
       }
       elseif($request->year!="" && $request->month=="" && $request->ytd=="" && $request->mtd=="" && Input::get('zero') === '1')
       {
           $inventories = $this->showOut();
           foreach($inventories as $key=>$inventory)
           {
               $sum_dosages = DB::table('dosages')
                   ->where('material_id', $inventory->id)
                   ->whereYear('created_at', '=', $request->year)
                   ->sum('quantity');

               $inventory->consum  = $sum_dosages;
           }

           return view('operations.outZeroInfo', compact('inventories'));
       }
       elseif($request->year!="" && $request->month!="" && $request->ytd=="" && $request->mtd=="" && $request->zero=="")
       {
           $inventories = $this->showOut();
           foreach($inventories as $key=>$inventory)
           {
               $sum_dosages = DB::table('dosages')
                   ->where('material_id', $inventory->id)
                   ->whereYear('created_at', '=', $request->year)
                   ->whereMonth('created_at', '=', $request->month)
                   ->sum('quantity');

               $inventory->consum  = $sum_dosages;
           }
           return view('operations.out.outInfo', compact('inventories'));
       }
       elseif($request->year!="" && $request->month!="" && $request->ytd=="" && $request->mtd=="" && Input::get('zero') === '1')
       {
           $inventories = $this->showOut();
           foreach($inventories as $key=>$inventory)
           {
               $sum_dosages = DB::table('dosages')
                   ->where('material_id', $inventory->id)
                   ->whereYear('created_at', '=', $request->year)
                   ->whereMonth('created_at', '=', $request->month)
                   ->sum('quantity');

               $inventory->consum  = $sum_dosages;
           }

           return view('operations.outZeroInfo', compact('inventories'));
       }
       elseif($request->year=="" && $request->month=="" && Input::get('ytd') === '1' && $request->mtd=="" && $request->zero=="")
       {
           $inventories = $this->showOut();
           foreach($inventories as $key=>$inventory)
           {
               $sum_dosages = DB::table('dosages')
                   ->where('material_id', $inventory->id)
                   ->whereYear('created_at', '=', Carbon::now()->year)
                   ->sum('quantity');

               $inventory->consum  = $sum_dosages;
           }
           return view('operations.out.outInfo', compact('inventories'));
       }
       elseif($request->year=="" && $request->month=="" && Input::get('ytd') === '1' && $request->mtd=="" && Input::get('zero') === '1')
       {
           $inventories = $this->showOut();
           foreach($inventories as $key=>$inventory)
           {
               $sum_dosages = DB::table('dosages')
                   ->where('material_id', $inventory->id)
                   ->whereYear('created_at', '=', Carbon::now()->year)
                   ->sum('quantity');

               $inventory->consum  = $sum_dosages;
           }

           return view('operations.outZeroInfo', compact('inventories'));
       }
       elseif($request->year=="" && $request->month=="" && $request->ytd=="" && Input::get('mtd') === '1' && $request->zero=="")
       {
           $inventories = $this->showOut();
           foreach($inventories as $key=>$inventory)
           {
               $sum_dosages = DB::table('dosages')
                   ->where('material_id', $inventory->id)
                   ->whereYear('created_at', '=', Carbon::now()->year)
                   ->whereMonth('created_at', '=', Carbon::now()->month)
                   ->sum('quantity');

               $inventory->consum  = $sum_dosages;
           }
           return view('operations.out.outInfo', compact('inventories'));
       }
       elseif($request->year=="" && $request->month=="" && $request->ytd=="" && Input::get('mtd') === '1' && Input::get('zero') === '1')
       {
           $inventories = $this->showOut();
           foreach($inventories as $key=>$inventory)
           {
               $sum_dosages = DB::table('dosages')
                   ->where('material_id', $inventory->id)
                   ->whereMonth('created_at', '=', Carbon::now()->month)
                   ->sum('quantity');

               $inventory->consum  = $sum_dosages;
           }

           return view('operations.outZeroInfo', compact('inventories'));
       }
   }

    public function showOut()
    {
        return Material::orderBy('name', 'ASC')
            ->get();
    }
}
