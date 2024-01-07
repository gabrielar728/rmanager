<?php

namespace App\Http\Controllers;

use App\Pump;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Material;
use Carbon\Carbon;

class PumpsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getManagePumps()
    {
        $materials = Material::orderBy('name', 'ASC')->get();

        return view('pumps.index', compact('materials'));
    }

    public function createPump(Request $request)
    {
        if ($request->ajax()) {
            return response(Pump::create($request->all()));
        }
    }

    public function showPumpInformation()
    {
        $pumps = $this->pumpInformation();
        return view('pumps.pumpsInfo', compact('pumps'));
    }

    public function pumpInformation()
    {
        return Pump::orderBy('id', 'DESC')->get();
    }

    public function edit($id)
    {
        $materials = DB::table('materials')->orderBy('name','ASC')->get();
        $pump = Pump::find($id);
        return view('pumps.edit',compact('pump', 'materials'));
    }

    public function update(Request $request, $id)
    {

        Pump::find($id)->update($request->all());
        return redirect()->route('administrare-pompe')
            ->with('success','Pompa actualizata!');
    }
}
