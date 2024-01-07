<?php

namespace App\Http\Controllers;

use App\ArticlesMaterial;
use Illuminate\Http\Request;
use App\Material;
use App\Row;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;

class MaterialsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getManageMaterial()
    {
        return view('materials.index');
    }

    public function createMaterial(Request $request)
    {
        if ($request->ajax()) {
            return response(Material::create($request->all()));
        }
    }

    public function showMaterialInformation(Request $request)
    {
        $materials = Material::orderBy('id', 'DESC')
            ->select('id as material_id', 'materials.*')
            ->get();

        foreach ($materials as $material)
        {
            $exist = ArticlesMaterial::where('material_id', $material->material_id)->value('material_id');
            if ($exist === null)
            {
                $material->exist = 0;
            } else {
                $material->exist = 1;
            }
        }
        return view('materials.materialsInfo', compact('materials'));
    }

    public function edit($id)
    {
        $material = Material::find($id);
        return view('materials.edit',compact('material'));
    }

    public function update(Request $request, $id)
    {
        $material = Material::findOrFail($id);

        $material->name = $request->name;

        $material->save();

        return redirect()->route('administrare-materiale');
    }
}

