<?php

namespace App\Http\Controllers;

use App\Article;
use App\ArticleGroup;
use App\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class GroupsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    function index(){
        $articles = DB::table('articles')->where('status', 1)->orderBy('name','ASC')->get();
        return view('groups.index', compact('articles'));
    }

    public function createGroup(Request $request)
    {
        $group = new Group();
        $group->name=$request->name;
        $group->created_at= Carbon::now();
        $group->updated_at= Carbon::now();

        if($group->save()) {
            $id = $group->id;
            $count = 1;

            foreach ($request->values_id as $key => $v) {
                $data = array(
                    'article_id' => $v,
                    'row' => $count,
                    'group_id' => $id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),);

                ArticleGroup::insert($data);
                $count++;
            }
            echo "done";
        } else {
            echo "Error";
        }
    }

    public function showGroupInformation()
    {
        $groups = Group::with('articles')
            ->where('id', '<>', 1)
            ->select('created_at as created_at_group', 'updated_at as updated_as_group', 'groups.*')
            ->orderBy('id', 'DESC')
            ->take(100)
            ->get();

        return view('groups.groupsInfo', compact('groups'));
    }

    public function articles_selected(Request $request) {
        $data = Article::where('id', $request->id)->get();
        return response()->json($data);
    }

    public function changeActiveStatus($group_id)
    {
        $group = Group::find($group_id);
        $group->status = 0;
        $group->save();

        return redirect()->route('groups.index');
    }

    public function changeInactiveStatus($group_id)
    {
        $group = Group::find($group_id);
        $group->status = 1;
        $group->save();

        return redirect()->route('groups.index');
    }

    public function GroupSort(Request $request) {
        $id = $request->id;
        $articles_groups = ArticleGroup::with('article')
            ->with('group')
            ->where('group_id', $id)
            ->get();
        return View::make('groups.articles_list')->with('articles_groups', $articles_groups)->with('id', $id);
    }

    public function saveSorting(Request $request)
    {
        ArticleGroup::where('group_id', $request->id)->delete();
        $count = 1;

        foreach ($request->values_id as $key => $v) {
            $data = array(
                'article_id' => $v,
                'row' => $count,
                'group_id' => $request->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),);

            ArticleGroup::insert($data);
            $count++;
        }

        return back();
    }

    public function verifGroupName(Request $request)
    {
        $exist = Group::where('name', '=', $request->name)->first();
        if($exist != null)
        {
            return response()->json(['success' => true, 'created'=> true, 'msg' => 'Numele grupului exista in baza de date.']);
        } else {
            return response()->json(['success' => true, 'created'=> false, 'msg' => 'OK']);
        }
    }
}
