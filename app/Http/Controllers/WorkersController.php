<?php

namespace App\Http\Controllers;

use App\Product;
use App\Worker;
use Illuminate\Http\Request;

class WorkersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getManageWorkers()
    {
        return view('workers.index');
    }

    public function createWorker(Request $request)
    {
        if ($request->ajax()) {
            $exist = Worker::where('card', '=', $request->card)->first();
            if ($exist == null) {
                return response(Worker::create($request->all()));
            }
        }
    }

    public function showWorkerInformation()
    {
        $workers = $this->workerInformation();
        foreach ($workers as $worker) {
            $exist = Product::where('worker_id', $worker->id)
                ->whereIn('status_id', [2, 4])->value('worker_id');
            if ($exist === null) {
                $worker->exist = 0;
            } else {
                $worker->exist = 1;
            }
        }
        return view('workers.workersInfo', compact('workers'));
    }

    public function workerInformation()
    {
        return Worker::orderBy('first', 'ASC')->get();
    }

    public function edit($id)
    {
        $worker = Worker::find($id);
        return view('workers.edit', compact('worker'));
    }

    public function update(Request $request, $id)
    {

        $worker = Worker::findOrFail($id);

        $worker->first = $request->first;
        $worker->last = $request->last;
        $worker->card = $request->card;
        $worker->status = $request->status;

        $worker->save();

        return redirect()->route('administrare-personal');
    }

    public function verifCard(Request $request)
    {
        $exist = Worker::where('card', '=', $request->card)->first();
        if ($exist != null) {
            return response()->json(['success' => true, 'created' => true, 'msg' => 'Numarul cardului exista in baza de date.']);
        } else {
            return response()->json(['success' => true, 'created' => false, 'msg' => 'OK']);
        }
    }

    public function workerStatus(Request $request)
    {
        $status = $request->status;
        $worker_id = $request->worker_id;
        $data = Worker::findOrFail($worker_id);

        $data->status = $status;
        $data->save();
    }
}
